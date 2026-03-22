<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\ExpoPushChannel;

class SettlementCompleted extends Notification implements ShouldQueue
{
    use Queueable;

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
        $url = url("/groups/{$this->group->id}/settlements");

        return (new MailMessage)
            ->subject("Settlement completed in {$this->group->name}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("**{$this->fromUserName}** has paid you **₹{$this->amount}** in **{$this->group->name}**.")
            ->action('View Settlements', $url)
            ->line('You can manage your notification preferences in your profile settings.');
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
