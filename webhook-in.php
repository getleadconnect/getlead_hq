<?php
/**
 * Getlead HQ — Inbound Webhook
 * Receives events from Getlead CRM and creates tasks/records in HQ
 * 
 * URL: https://akhilkrishna.com/hq/webhook-in.php
 * Method: POST
 * Auth: X-Webhook-Token header or webhook_token in body
 */

require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-Webhook-Token');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

// Auth
define('WEBHOOK_IN_TOKEN', 'glhq_wh_8f3a2c6d9e1b4a7052df');

$token = $_SERVER['HTTP_X_WEBHOOK_TOKEN'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);
if (!$token) $token = $input['webhook_token'] ?? '';

if ($token !== WEBHOOK_IN_TOKEN) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Invalid webhook token']);
    exit;
}

$db = getDB();
$event = $input['event'] ?? '';

// Log all incoming webhooks
$db->exec("CREATE TABLE IF NOT EXISTS webhook_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event TEXT,
    payload TEXT,
    result TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

function logWebhook($db, $event, $payload, $result) {
    $stmt = $db->prepare("INSERT INTO webhook_log (event, payload, result, created_at) VALUES (:e, :p, :r, :now)");
    $stmt->execute([':e' => $event, ':p' => json_encode($payload), ':r' => json_encode($result), ':now' => date('Y-m-d H:i:s')]);
}

function createTask($db, $title, $description, $category, $priority, $dueDate, $assignTo = null) {
    $stmt = $db->prepare("INSERT INTO tasks (title, description, assigned_to, created_by, priority, status, due_date, category, created_at, updated_at) 
        VALUES (:title, :desc, :assigned, 1, :priority, 'pending', :due, :cat, :now, :now)");
    $stmt->execute([
        ':title' => $title,
        ':desc' => $description,
        ':assigned' => $assignTo,
        ':priority' => $priority,
        ':due' => $dueDate,
        ':cat' => $category,
        ':now' => date('Y-m-d H:i:s'),
    ]);
    $taskId = $db->lastInsertId();
    $db->prepare("INSERT INTO task_history (task_id, staff_id, action, new_value, created_at) VALUES (:tid, 1, 'created', :val, :now)")
        ->execute([':tid' => $taskId, ':val' => $title, ':now' => date('Y-m-d H:i:s')]);
    return $taskId;
}

// Find staff by name (fuzzy) or ID
function findStaff($db, $identifier) {
    if (!$identifier) return null;
    if (is_numeric($identifier)) {
        return $db->query("SELECT id FROM staff WHERE id = " . intval($identifier) . " AND active = 1")->fetchColumn() ?: null;
    }
    $stmt = $db->prepare("SELECT id FROM staff WHERE active = 1 AND (name = :n OR name LIKE :nl) LIMIT 1");
    $stmt->execute([':n' => $identifier, ':nl' => $identifier . '%']);
    return $stmt->fetchColumn() ?: null;
}

$result = ['ok' => false, 'error' => 'Unknown event'];

switch ($event) {

    // =========================================
    // NEW LEAD
    // =========================================
    case 'lead.new':
        $name = trim($input['lead_name'] ?? $input['name'] ?? 'Unknown');
        $company = trim($input['company'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $email = trim($input['email'] ?? '');
        $source = trim($input['source'] ?? '');
        $value = trim($input['value'] ?? '');
        $assignee = $input['assign_to'] ?? null;
        $notes = trim($input['notes'] ?? '');

        $title = "New Lead: $name" . ($company ? " ($company)" : '');
        $desc = "📞 Phone: $phone\n✉️ Email: $email";
        if ($source) $desc .= "\n📍 Source: $source";
        if ($value) $desc .= "\n💰 Value: ₹$value";
        if ($notes) $desc .= "\n📝 $notes";

        $staffId = findStaff($db, $assignee);
        $due = date('Y-m-d', strtotime('+1 day'));
        $taskId = createTask($db, $title, $desc, 'sales', 'high', $due, $staffId);

        $result = ['ok' => true, 'task_id' => $taskId, 'message' => "Lead task created"];
        break;

    // =========================================
    // LEAD ASSIGNED
    // =========================================
    case 'lead.assigned':
        $name = trim($input['lead_name'] ?? $input['name'] ?? 'Unknown');
        $company = trim($input['company'] ?? '');
        $assignee = $input['assign_to'] ?? null;
        $phone = trim($input['phone'] ?? '');
        $notes = trim($input['notes'] ?? '');

        $title = "Follow up: $name" . ($company ? " ($company)" : '');
        $desc = "Assigned for follow-up.";
        if ($phone) $desc .= "\n📞 $phone";
        if ($notes) $desc .= "\n📝 $notes";

        $staffId = findStaff($db, $assignee);
        $due = date('Y-m-d', strtotime('+1 day'));
        $taskId = createTask($db, $title, $desc, 'sales', 'normal', $due, $staffId);

        $result = ['ok' => true, 'task_id' => $taskId];
        break;

    // =========================================
    // RENEWAL DUE
    // =========================================
    case 'renewal.due':
        $customer = trim($input['customer'] ?? $input['company'] ?? 'Unknown');
        $amount = trim($input['amount'] ?? '');
        $expiryDate = trim($input['expiry_date'] ?? '');
        $daysLeft = intval($input['days_left'] ?? 0);
        $contact = trim($input['contact'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $assignee = $input['assign_to'] ?? null;
        $plan = trim($input['plan'] ?? '');

        $title = "Renewal: $customer" . ($amount ? " (₹$amount)" : '');
        $desc = "License renewal due.";
        if ($plan) $desc .= "\n📋 Plan: $plan";
        if ($expiryDate) $desc .= "\n📅 Expires: $expiryDate";
        if ($daysLeft) $desc .= "\n⏳ Days left: $daysLeft";
        if ($contact) $desc .= "\n👤 Contact: $contact";
        if ($phone) $desc .= "\n📞 $phone";
        if ($amount) $desc .= "\n💰 Revenue at risk: ₹$amount";

        $priority = $daysLeft <= 7 ? 'urgent' : ($daysLeft <= 15 ? 'high' : 'normal');
        $staffId = findStaff($db, $assignee);
        $due = $expiryDate ?: date('Y-m-d', strtotime('+7 days'));
        $taskId = createTask($db, $title, $desc, 'sales', $priority, $due, $staffId);

        $result = ['ok' => true, 'task_id' => $taskId];
        break;

    // =========================================
    // RENEWAL COMPLETED
    // =========================================
    case 'renewal.completed':
        $customer = trim($input['customer'] ?? $input['company'] ?? '');
        $amount = trim($input['amount'] ?? '');

        // Find and complete matching renewal task
        if ($customer) {
            $task = $db->prepare("SELECT id FROM tasks WHERE title LIKE :t AND status != 'done' ORDER BY created_at DESC LIMIT 1");
            $task->execute([':t' => "Renewal: $customer%"]);
            $tid = $task->fetchColumn();
            if ($tid) {
                $db->prepare("UPDATE tasks SET status = 'done', completed_at = :now, updated_at = :now WHERE id = :id")
                    ->execute([':now' => date('Y-m-d H:i:s'), ':id' => $tid]);
                $db->prepare("INSERT INTO task_history (task_id, staff_id, action, old_value, new_value, created_at) VALUES (:tid, 1, 'status_changed', 'pending', 'done', :now)")
                    ->execute([':tid' => $tid, ':now' => date('Y-m-d H:i:s')]);
                $db->prepare("INSERT INTO task_comments (task_id, staff_id, comment, created_at) VALUES (:tid, 1, :c, :now)")
                    ->execute([':tid' => $tid, ':c' => "✅ Renewal completed" . ($amount ? " — ₹$amount" : ''), ':now' => date('Y-m-d H:i:s')]);
            }
            $result = ['ok' => true, 'task_id' => $tid ?: null, 'message' => $tid ? 'Renewal task completed' : 'No matching task found'];
        } else {
            $result = ['ok' => false, 'error' => 'Customer name required'];
        }
        break;

    // =========================================
    // SUPPORT TICKET
    // =========================================
    case 'ticket.new':
        $ticketId = trim($input['ticket_id'] ?? '');
        $customer = trim($input['customer'] ?? $input['company'] ?? 'Unknown');
        $subject = trim($input['subject'] ?? 'Support request');
        $description = trim($input['description'] ?? '');
        $priority = $input['priority'] ?? 'normal';
        $assignee = $input['assign_to'] ?? null;
        $phone = trim($input['phone'] ?? '');

        // Map CRM priority to HQ priority
        $priorityMap = ['critical' => 'urgent', 'urgent' => 'urgent', 'high' => 'high', 'medium' => 'normal', 'normal' => 'normal', 'low' => 'low'];
        $priority = $priorityMap[$priority] ?? 'normal';

        $title = "Ticket" . ($ticketId ? " #$ticketId" : '') . ": $subject";
        $desc = "👤 Customer: $customer";
        if ($phone) $desc .= "\n📞 $phone";
        if ($ticketId) $desc .= "\n🎫 Ticket ID: $ticketId";
        if ($description) $desc .= "\n\n$description";

        $staffId = findStaff($db, $assignee);
        $due = $priority === 'urgent' ? date('Y-m-d') : date('Y-m-d', strtotime('+2 days'));
        $taskId = createTask($db, $title, $desc, 'support', $priority, $due, $staffId);

        $result = ['ok' => true, 'task_id' => $taskId];
        break;

    // =========================================
    // TICKET RESOLVED
    // =========================================
    case 'ticket.resolved':
        $ticketId = trim($input['ticket_id'] ?? '');
        $resolution = trim($input['resolution'] ?? 'Resolved');

        if ($ticketId) {
            $task = $db->prepare("SELECT id FROM tasks WHERE title LIKE :t AND status != 'done' ORDER BY created_at DESC LIMIT 1");
            $task->execute([':t' => "%#$ticketId%"]);
            $tid = $task->fetchColumn();
            if ($tid) {
                $db->prepare("UPDATE tasks SET status = 'done', completed_at = :now, updated_at = :now WHERE id = :id")
                    ->execute([':now' => date('Y-m-d H:i:s'), ':id' => $tid]);
                $db->prepare("INSERT INTO task_comments (task_id, staff_id, comment, created_at) VALUES (:tid, 1, :c, :now)")
                    ->execute([':tid' => $tid, ':c' => "✅ $resolution", ':now' => date('Y-m-d H:i:s')]);
            }
            $result = ['ok' => true, 'task_id' => $tid ?: null];
        } else {
            $result = ['ok' => false, 'error' => 'ticket_id required'];
        }
        break;

    // =========================================
    // PAYMENT / SALE
    // =========================================
    case 'payment.received':
        $customer = trim($input['customer'] ?? $input['company'] ?? 'Unknown');
        $amount = trim($input['amount'] ?? '0');
        $type = trim($input['type'] ?? 'payment'); // new, renewal, upgrade
        $plan = trim($input['plan'] ?? '');
        $invoiceId = trim($input['invoice_id'] ?? '');
        $rep = $input['sales_rep'] ?? $input['assign_to'] ?? null;

        $title = "💰 Payment: $customer — ₹$amount";
        $desc = "Payment received.";
        if ($type) $desc .= "\n📋 Type: $type";
        if ($plan) $desc .= "\n📦 Plan: $plan";
        if ($invoiceId) $desc .= "\n🧾 Invoice: $invoiceId";

        $staffId = findStaff($db, $rep);
        $taskId = createTask($db, $title, $desc, 'sales', 'normal', date('Y-m-d'), $staffId);
        // Auto-complete payment tasks
        $db->prepare("UPDATE tasks SET status = 'done', completed_at = :now WHERE id = :id")
            ->execute([':now' => date('Y-m-d H:i:s'), ':id' => $taskId]);

        $result = ['ok' => true, 'task_id' => $taskId];
        break;

    // =========================================
    // SALES DATA / DAILY SYNC
    // =========================================
    case 'sales.daily_sync':
        $date = $input['date'] ?? date('Y-m-d');
        $repName = trim($input['rep_name'] ?? '');
        $data = $input['data'] ?? [];

        // Map to daily report format
        $staffId = findStaff($db, $repName);
        if ($staffId && !empty($data)) {
            $reportData = json_encode($data);
            $existing = $db->prepare("SELECT id FROM daily_reports WHERE staff_id = :sid AND report_date = :d");
            $existing->execute([':sid' => $staffId, ':d' => $date]);
            $existingId = $existing->fetchColumn();
            if ($existingId) {
                $db->prepare("UPDATE daily_reports SET report_data = :data, updated_at = :now WHERE id = :id")
                    ->execute([':data' => $reportData, ':now' => date('Y-m-d H:i:s'), ':id' => $existingId]);
            } else {
                $db->prepare("INSERT INTO daily_reports (staff_id, report_date, report_data, submitted_at) VALUES (:sid, :date, :data, :now)")
                    ->execute([':sid' => $staffId, ':date' => $date, ':data' => $reportData, ':now' => date('Y-m-d H:i:s')]);
            }
            $result = ['ok' => true, 'staff_id' => $staffId, 'updated' => !!$existingId];
        } else {
            $result = ['ok' => false, 'error' => $staffId ? 'No data provided' : 'Staff not found'];
        }
        break;

    // =========================================
    // GENERIC TASK
    // =========================================
    case 'task.create':
        $title = trim($input['title'] ?? '');
        if (!$title) { $result = ['ok' => false, 'error' => 'title required']; break; }
        $desc = trim($input['description'] ?? '');
        $category = $input['category'] ?? 'other';
        $priority = $input['priority'] ?? 'normal';
        $due = $input['due_date'] ?? null;
        $assignee = $input['assign_to'] ?? null;
        $staffId = findStaff($db, $assignee);

        $taskId = createTask($db, $title, $desc, $category, $priority, $due, $staffId);
        $result = ['ok' => true, 'task_id' => $taskId];
        break;

    // =========================================
    // PING / TEST
    // =========================================
    case 'ping':
        $result = ['ok' => true, 'message' => 'pong', 'time' => date('c')];
        break;
}

logWebhook($db, $event, $input, $result);
echo json_encode($result);
