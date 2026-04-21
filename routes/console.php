<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Notify Telegram group about staff who have not submitted daily reports — every day 8:00 PM IST
Schedule::command('reports:notify-missing')
    ->dailyAt('10:45')
    ->timezone('Asia/Kolkata')
    ->weekdays();
    
    // ->onOneServer();
