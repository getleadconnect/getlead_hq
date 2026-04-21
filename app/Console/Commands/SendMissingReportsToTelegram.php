<?php

namespace App\Console\Commands;

use App\Models\DailyReport;
use App\Models\Staff;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMissingReportsToTelegram extends Command
{

   protected $signature = 'reports:notify-missing {--date= : Date to check (Y-m-d), defaults to today}';

    protected $description = 'Send list of staff who have not submitted daily report to Telegram';

    public function handle(): int
    {

     \Log::info("Schedule message telegram : started");
     
        $token  = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (! $token || ! $chatId) {
            $this->error('TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID not configured in .env');
            return self::FAILURE;
        }

        $date = $this->option('date') ?: Carbon::today()->toDateString();

        if (Carbon::parse($date)->isWeekend()) {
            $this->info("Skipping weekend: $date");
            return self::SUCCESS;
        }

        $submittedIds = DailyReport::where('report_date', $date)->pluck('staff_id')->toArray();

        $activeStaff = Staff::where('active', 1)
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        $submitted = $activeStaff->whereIn('id', $submittedIds)->values();
        $pending   = $activeStaff->whereNotIn('id', $submittedIds)->values();

        $dateLabel = Carbon::parse($date)->format('D, d M Y');

        $fmtList = function ($list) {
            if ($list->isEmpty()) return "  —";
            return $list->map(function ($s, $i) {
                $num  = $i + 1;
                $role = ucwords(str_replace('_', ' ', $s->role));
                return "{$num}. <b>" . e($s->name) . "</b> — <i>{$role}</i>";
            })->implode("\n");
        };

        $text  = "📊 <b>Daily Reports</b>\n";
        $text .= "📅 {$dateLabel}\n";
        $text .= "✅ Submitted: <b>" . $submitted->count() . "</b>\n";
        $text .= "⚠️ Pending: <b>" . $pending->count() . "</b>\n\n";
        $text .= "✅ <b>Submitted Staff</b>\n";
        $text .= $fmtList($submitted) . "\n\n";
        $text .= "⚠️ <b>Pending Staff</b>\n";
        $text .= $fmtList($pending);

        \Log::info("message-text:".$text);
        \Log::info('reports:notify-missing STARTED');

        try {
            $res = Http::timeout(15)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => 'HTML',
            ]);

            if ($res->successful()) {
                \Log::info('Telegram notification sent. Submitted: ' . $submitted->count() . ', Pending: ' . $pending->count());
               
                $this->info('Telegram notification sent. Submitted: ' . $submitted->count() . ', Pending: ' . $pending->count());
                return self::SUCCESS;
            }

            $this->error('Telegram API error: ' . $res->body());
            \Log::error('Telegram missing-reports notify failed', ['response' => $res->body()]);
            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Exception: ' . $e->getMessage());
            \Log::error('Telegram missing-reports exception', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }
    }
}
