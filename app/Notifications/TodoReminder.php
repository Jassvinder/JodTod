<?php

namespace App\Notifications;

use App\Models\Todo;
use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TodoReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Todo $todo,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'todo_reminder',
            'todo_id' => $this->todo->id,
            'title' => $this->todo->title,
            'priority' => $this->todo->priority,
            'due_date' => $this->todo->due_date?->toDateString(),
            'message' => "Reminder: {$this->todo->title}",
        ];
    }
}
