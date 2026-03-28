<?php

namespace App\Console\Commands;

use App\Models\Todo;
use App\Notifications\TodoReminder;
use Illuminate\Console\Command;

class SendTodoReminders extends Command
{
    protected $signature = 'todos:send-reminders';

    protected $description = 'Send notifications for todos with due reminders';

    public function handle(): int
    {
        $todos = Todo::with('user')
            ->where('is_completed', false)
            ->where('reminder_sent', false)
            ->whereNotNull('reminder_at')
            ->where('reminder_at', '<=', now())
            ->get();

        $count = 0;

        foreach ($todos as $todo) {
            $todo->user->notify(new TodoReminder($todo));
            $todo->update(['reminder_sent' => true]);
            $count++;
        }

        if ($count > 0) {
            $this->info("Sent {$count} todo reminder(s).");
        }

        return self::SUCCESS;
    }
}
