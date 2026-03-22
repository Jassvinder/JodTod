<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\ExpoPushChannel;

class AddedToGroup extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Group $group,
        private string $addedByName,
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
        $url = url("/groups/{$this->group->id}");

        return (new MailMessage)
            ->subject("You were added to {$this->group->name}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$this->addedByName} added you to the group **{$this->group->name}**.")
            ->action('View Group', $url)
            ->line('You can manage your notification preferences in your profile settings.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'added_to_group',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'added_by' => $this->addedByName,
            'message' => "{$this->addedByName} added you to \"{$this->group->name}\".",
        ];
    }
}
