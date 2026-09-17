<?php

namespace App\Notifications;

use App\Models\Group;
use App\Notifications\Channels\ExpoPushChannel;
use App\Notifications\Concerns\HasBrandedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SettlementCompleted extends Notification implements ShouldQueue
{
    use HasBrandedMail, Queueable;

    public function __construct(
        private Group $group,
        private float $amount,
        private string $fromUserName,
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
        return $this->brandedMailMessage(
            "Settlement completed in {$this->group->name}",
            "Hello {$notifiable->name}!",
            [
                "{$this->fromUserName} has paid you ₹{$this->amount} in {$this->group->name}.",
                'Your group balance has been updated.',
            ],
            'View Settlements',
            url("/groups/{$this->group->id}/settlements"),
            'Click the button below to view the settlement details.',
            'Best regards, JodTod'
        );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'settlement_completed',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'amount' => $this->amount,
            'from_user_name' => $this->fromUserName,
            'message' => "{$this->fromUserName} paid you ₹{$this->amount} in {$this->group->name}.",
        ];
    }
}
