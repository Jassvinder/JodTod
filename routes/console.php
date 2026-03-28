<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('todos:send-reminders')->everyMinute();
Schedule::command('summary:weekly')->weeklyOn(1, '08:00'); // Monday 8 AM
