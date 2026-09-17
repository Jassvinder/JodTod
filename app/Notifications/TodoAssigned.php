<?php

namespace App\Notifications;

use App\Models\Todo;
use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TodoAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Todo $todo,
        private string $assignerName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'todo_assigned',
            'todo_id' => $this->todo->id,
            'title' => $this->todo->title,
            'priority' => $this->todo->priority,
            'assigned_by' => $this->assignerName,
            'message' => "{$this->assignerName} assigned you: {$this->todo->title}",
        ];
    }
}
