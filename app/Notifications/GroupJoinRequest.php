<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupJoinRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Group $group,
        private User $requestingUser,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notification_email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/groups/{$this->group->id}");

        return (new MailMessage)
            ->subject("{$this->requestingUser->name} wants to join {$this->group->name}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$this->requestingUser->name} has requested to join your group **{$this->group->name}**.")
            ->action('Review Request', $url)
            ->line('You can approve or reject this request from the group detail page.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'group_join_request',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'requesting_user_id' => $this->requestingUser->id,
            'requesting_user_name' => $this->requestingUser->name,
            'message' => "{$this->requestingUser->name} wants to join \"{$this->group->name}\".",
        ];
    }
}
