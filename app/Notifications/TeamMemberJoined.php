<?php

namespace App\Notifications;

use App\Models\Team;
use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Sent to everyone above a new member: their direct sponsor and the whole upline.
 */
class TeamMemberJoined extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Team $team,
        private string $memberName,
        private string $sponsorName,
        private int $sponsorId,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $under = $notifiable->id === $this->sponsorId ? 'with your invite code' : "under {$this->sponsorName}";

        return [
            'type' => 'team_member_joined',
            'team_id' => $this->team->id,
            'message' => "{$this->memberName} joined {$this->team->name} {$under}.",
        ];
    }
}
