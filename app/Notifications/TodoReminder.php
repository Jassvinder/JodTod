<?php

namespace App\Notifications;

use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\ExpoPushChannel;

class TodoReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Todo $todo,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database', ExpoPushChannel::class];

        if ($notifiable->notification_email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/todos');

        $dueInfo = $this->todo->due_date
            ? " (Due: {$this->todo->due_date->format('d M Y')})"
            : '';

        return (new MailMessage)
            ->subject("Task Reminder: {$this->todo->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Reminder for your task: **{$this->todo->title}**{$dueInfo}")
            ->line("Priority: " . ucfirst($this->todo->priority))
            ->action('View Tasks', $url)
            ->line('You can manage your notification preferences in your profile settings.');
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
