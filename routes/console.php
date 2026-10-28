<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('todos:send-reminders')->everyMinute();
Schedule::command('summary:weekly')->weeklyOn(1, '08:00'); // Monday 8 AM
Schedule::command('team-builder:weekly-summary')->weeklyOn(1, '08:30'); // Monday 8:30 AM

// Shared hosting has no long-running worker, so the per-minute cron drains the queue (emails, push notifications).
Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();
