<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskHistory;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    // Fallback token (can be overridden by settings.webhook_in_token)
    const DEFAULT_TOKEN = 'glhq_wh_8f3a2c6d9e1b4a7052df';

    // ── Auth ──────────────────────────────────────────────────────────────────

    private function validateToken(Request $request): bool
    {
        $expected = Setting::get('webhook_in_token', self::DEFAULT_TOKEN);
        $token    = $request->header('X-Webhook-Token')
                 ?? $request->input('webhook_token', '');
        return $token === $expected;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Find staff by numeric ID or by name (exact then starts-with).
     */
    private function findStaff(mixed $identifier): ?int
    {
        if (!$identifier) return null;

        if (is_numeric($identifier)) {
            return Staff::where('id', (int) $identifier)->where('active', true)->value('id');
        }

        return Staff::where('active', true)
            ->where(fn($q) => $q->where('name', $identifier)->orWhere('name', 'like', $identifier . '%'))
            ->value('id');
    }

    /**
     * Create a task + history record. Created-by defaults to staff ID 1 (system/admin).
     */

    private function createTask(
        string  $title,
        string  $description,
        string  $category,
        string  $priority,
        ?string $dueDate,
        ?int    $assignedTo
    ): Task {
        $task = Task::create([
            'title'       => $title,
            'description' => $description,
            'assigned_to' => $assignedTo,
            'created_by'  => 1,
            'priority'    => $priority,
            'status'      => 'pending',
            'category'    => $category,
            'due_date'    => $dueDate,
        ]);

        TaskHistory::create([
            'task_id'   => $task->id,
            'staff_id'  => 1,
            'action'    => 'created',
            'new_value' => $title,
        ]);

        return $task;
    }

    /**
     * Mark a task as done with an optional comment.
     */

    private function completeTask(Task $task, string $comment = ''): void
    {
        $old           = $task->status;
        $task->status  = 'done';
        $task->completed_at = now();
        $task->save();

        TaskHistory::create([
            'task_id'   => $task->id,
            'staff_id'  => 1,
            'action'    => 'status_changed',
            'old_value' => $old,
            'new_value' => 'done',
        ]);

        if ($comment) {
            TaskComment::create([
                'task_id'  => $task->id,
                'staff_id' => 1,
                'comment'  => $comment,
            ]);
        }
    }

    /**
     * Persist webhook log entry.
     */
    private function logWebhook(string $event, array $payload, array $result): void
    {
        try {
            WebhookLog::create([
                'event'   => $event,
                'payload' => $payload,
                'result'  => $result,
            ]);
        } catch (\Throwable) {}
    }

    // ── Main entry point ──────────────────────────────────────────────────────

    public function inbound(Request $request)
    {
        // Token auth
        if (!$this->validateToken($request)) {
            return response()->json(['ok' => false, 'error' => 'Invalid webhook token'], 401);
        }

        $input = $request->all();
        $event = $input['event'] ?? '';
        $result = ['ok' => false, 'error' => 'Unknown event'];

        try {
            $result = match ($event) {
                'lead.new'           => $this->handleLeadNew($input),
                'lead.assigned'      => $this->handleLeadAssigned($input),
                'renewal.due'        => $this->handleRenewalDue($input),
                'renewal.completed'  => $this->handleRenewalCompleted($input),
                'ticket.new'         => $this->handleTicketNew($input),
                'ticket.resolved'    => $this->handleTicketResolved($input),
                'payment.received'   => $this->handlePaymentReceived($input),
                'sales.daily_sync'   => $this->handleSalesDailySync($input),
                'task.create'        => $this->handleTaskCreate($input),
                'ping'               => ['ok' => true, 'message' => 'pong', 'time' => now()->toIso8601String()],
                default              => ['ok' => false, 'error' => 'Unknown event'],
            };
        } catch (\Throwable $e) {
            $result = ['ok' => false, 'error' => $e->getMessage()];
        }

        $this->logWebhook($event, $input, $result);

        return response()->json($result);
    }

    // ── Event Handlers ────────────────────────────────────────────────────────

    /**
     * lead.new — Incoming new lead from CRM.
     * Creates a high-priority sales task due tomorrow.
     */
    
    private function handleLeadNew(array $input): array
    {
        $name     = trim($input['lead_name'] ?? $input['name'] ?? 'Unknown');
        $company  = trim($input['company'] ?? '');
        $phone    = trim($input['phone'] ?? '');
        $email    = trim($input['email'] ?? '');
        $source   = trim($input['source'] ?? '');
        $value    = trim($input['value'] ?? '');
        $notes    = trim($input['notes'] ?? '');
        $assignee = $input['assign_to'] ?? null;

        $title = 'New Lead: ' . $name . ($company ? " ($company)" : '');
        $desc  = "📞 Phone: $phone\n✉️ Email: $email";
        if ($source) $desc .= "\n📍 Source: $source";
        if ($value)  $desc .= "\n💰 Value: ₹$value";
        if ($notes)  $desc .= "\n📝 $notes";

        $staffId = $this->findStaff($assignee);
        $due     = now()->addDay()->toDateString();
        $task    = $this->createTask($title, $desc, 'sales', 'high', $due, $staffId);

        return ['ok' => true, 'task_id' => $task->id, 'message' => 'Lead task created'];
    }

    /**
     * lead.assigned — Lead assigned to a rep for follow-up.
     * Creates a normal-priority sales task due tomorrow.
     */
    private function handleLeadAssigned(array $input): array
    {
        $name     = trim($input['lead_name'] ?? $input['name'] ?? 'Unknown');
        $company  = trim($input['company'] ?? '');
        $phone    = trim($input['phone'] ?? '');
        $notes    = trim($input['notes'] ?? '');
        $assignee = $input['assign_to'] ?? null;

        $title = 'Follow up: ' . $name . ($company ? " ($company)" : '');
        $desc  = 'Assigned for follow-up.';
        if ($phone) $desc .= "\n📞 $phone";
        if ($notes) $desc .= "\n📝 $notes";

        $staffId = $this->findStaff($assignee);
        $due     = now()->addDay()->toDateString();
        $task    = $this->createTask($title, $desc, 'sales', 'normal', $due, $staffId);

        return ['ok' => true, 'task_id' => $task->id];
    }

    /**
     * renewal.due — License renewal approaching.
     * Priority: urgent (≤7 days), high (≤15 days), normal (>15 days).
     */
    private function handleRenewalDue(array $input): array
    {
        $customer   = trim($input['customer'] ?? $input['company'] ?? 'Unknown');
        $amount     = trim($input['amount'] ?? '');
        $expiryDate = trim($input['expiry_date'] ?? '');
        $daysLeft   = (int) ($input['days_left'] ?? 0);
        $contact    = trim($input['contact'] ?? '');
        $phone      = trim($input['phone'] ?? '');
        $plan       = trim($input['plan'] ?? '');
        $assignee   = $input['assign_to'] ?? null;

        $priority = $daysLeft <= 7 ? 'urgent' : ($daysLeft <= 15 ? 'high' : 'normal');

        $title = 'Renewal: ' . $customer . ($amount ? " (₹$amount)" : '');
        $desc  = 'License renewal due.';
        if ($plan)       $desc .= "\n📋 Plan: $plan";
        if ($expiryDate) $desc .= "\n📅 Expires: $expiryDate";
        if ($daysLeft)   $desc .= "\n⏳ Days left: $daysLeft";
        if ($contact)    $desc .= "\n👤 Contact: $contact";
        if ($phone)      $desc .= "\n📞 $phone";
        if ($amount)     $desc .= "\n💰 Revenue at risk: ₹$amount";

        $staffId = $this->findStaff($assignee);
        $due     = $expiryDate ?: now()->addWeek()->toDateString();
        $task    = $this->createTask($title, $desc, 'sales', $priority, $due, $staffId);

        return ['ok' => true, 'task_id' => $task->id];
    }

    /**
     * renewal.completed — License renewed by customer.
     * Finds the matching renewal task (by title prefix), marks it done.
     */
    private function handleRenewalCompleted(array $input): array
    {
        $customer = trim($input['customer'] ?? $input['company'] ?? '');
        $amount   = trim($input['amount'] ?? '');

        if (!$customer) {
            return ['ok' => false, 'error' => 'Customer name required'];
        }

        $task = Task::where('title', 'like', "Renewal: $customer%")
            ->where('status', '!=', 'done')
            ->orderByDesc('created_at')
            ->first();

        if ($task) {
            $comment = '✅ Renewal completed' . ($amount ? " — ₹$amount" : '');
            $this->completeTask($task, $comment);
        }

        return [
            'ok'      => true,
            'task_id' => $task?->id,
            'message' => $task ? 'Renewal task completed' : 'No matching task found',
        ];
    }

    /**
     * ticket.new — New support ticket from CRM.
     * Creates a support task; priority is mapped from CRM terms.
     * Urgent tickets are due today, others in 2 days.
     */
    private function handleTicketNew(array $input): array
    {
        $ticketId   = trim($input['ticket_id'] ?? '');
        $customer   = trim($input['customer'] ?? $input['company'] ?? 'Unknown');
        $subject    = trim($input['subject'] ?? 'Support request');
        $desc       = trim($input['description'] ?? '');
        $phone      = trim($input['phone'] ?? '');
        $assignee   = $input['assign_to'] ?? null;

        $priorityMap = [
            'critical' => 'urgent', 'urgent' => 'urgent',
            'high'     => 'high',
            'medium'   => 'normal', 'normal' => 'normal',
            'low'      => 'low',
        ];
        $priority = $priorityMap[$input['priority'] ?? 'normal'] ?? 'normal';

        $title    = 'Ticket' . ($ticketId ? " #$ticketId" : '') . ": $subject";
        $fullDesc = "👤 Customer: $customer";
        if ($phone)    $fullDesc .= "\n📞 $phone";
        if ($ticketId) $fullDesc .= "\n🎫 Ticket ID: $ticketId";
        if ($desc)     $fullDesc .= "\n\n$desc";

        $staffId = $this->findStaff($assignee);
        $due     = $priority === 'urgent' ? now()->toDateString() : now()->addDays(2)->toDateString();
        $task    = $this->createTask($title, $fullDesc, 'support', $priority, $due, $staffId);

        return ['ok' => true, 'task_id' => $task->id];
    }

    /**
     * ticket.resolved — Support ticket closed.
     * Finds task by ticket ID in title, marks done.
     */
    private function handleTicketResolved(array $input): array
    {
        $ticketId  = trim($input['ticket_id'] ?? '');
        $resolution = trim($input['resolution'] ?? 'Resolved');

        if (!$ticketId) {
            return ['ok' => false, 'error' => 'ticket_id required'];
        }

        $task = Task::where('title', 'like', "%#$ticketId%")
            ->where('status', '!=', 'done')
            ->orderByDesc('created_at')
            ->first();

        if ($task) {
            $this->completeTask($task, "✅ $resolution");
        }

        return ['ok' => true, 'task_id' => $task?->id];
    }

    /**
     * payment.received — Payment confirmed from CRM.
     * Creates a sales task and immediately marks it done (for record-keeping).
     */
    private function handlePaymentReceived(array $input): array
    {
        $customer  = trim($input['customer'] ?? $input['company'] ?? 'Unknown');
        $amount    = trim($input['amount'] ?? '0');
        $type      = trim($input['type'] ?? 'payment');
        $plan      = trim($input['plan'] ?? '');
        $invoiceId = trim($input['invoice_id'] ?? '');
        $rep       = $input['sales_rep'] ?? $input['assign_to'] ?? null;

        $title = "💰 Payment: $customer — ₹$amount";
        $desc  = 'Payment received.';
        if ($type)      $desc .= "\n📋 Type: $type";
        if ($plan)      $desc .= "\n📦 Plan: $plan";
        if ($invoiceId) $desc .= "\n🧾 Invoice: $invoiceId";

        $staffId = $this->findStaff($rep);
        $task    = $this->createTask($title, $desc, 'sales', 'normal', now()->toDateString(), $staffId);

        // Auto-complete: payment tasks are informational, already done
        $this->completeTask($task, '✅ Payment recorded automatically');

        return ['ok' => true, 'task_id' => $task->id];
    }

    /**
     * sales.daily_sync — CRM pushes a rep's daily stats.
     * Upserts a daily_report row for that staff member.
     */
    private function handleSalesDailySync(array $input): array
    {
        $date    = $input['date'] ?? now()->toDateString();
        $repName = trim($input['rep_name'] ?? '');
        $data    = $input['data'] ?? [];

        $staffId = $this->findStaff($repName);

        if (!$staffId) {
            return ['ok' => false, 'error' => 'Staff not found'];
        }
        if (empty($data)) {
            return ['ok' => false, 'error' => 'No data provided'];
        }

        $existing = DailyReport::where('staff_id', $staffId)->where('report_date', $date)->first();
        $isUpdate = (bool) $existing;

        if ($existing) {
            $existing->report_data = $data;
            $existing->updated_at  = now();
            $existing->save();
        } else {
            DailyReport::create([
                'staff_id'    => $staffId,
                'report_date' => $date,
                'report_data' => $data,
                'submitted_at' => now(),
            ]);
        }

        return ['ok' => true, 'staff_id' => $staffId, 'updated' => $isUpdate];
    }

    /**
     * task.create — Generic task creation from any external system.
     */
    private function handleTaskCreate(array $input): array
    {
        $title = trim($input['title'] ?? '');
        if (!$title) {
            return ['ok' => false, 'error' => 'title required'];
        }

        $staffId = $this->findStaff($input['assign_to'] ?? null);
        $task    = $this->createTask(
            $title,
            trim($input['description'] ?? ''),
            $input['category'] ?? 'other',
            $input['priority'] ?? 'normal',
            $input['due_date'] ?? null,
            $staffId
        );

        return ['ok' => true, 'task_id' => $task->id];
    }
}
