<?php

namespace App\Notifications;

use App\Models\Team;
use App\Models\TeamBuilderTarget;
use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TeamTargetSet extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Team $team,
        private string $setByName,
        private TeamBuilderTarget $target,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'team_target_set',
            'team_id' => $this->team->id,
            'message' => "{$this->setByName} set your weekly target: {$this->target->plans_per_week} plans and {$this->target->follow_ups_per_week} follow-ups.",
        ];
    }
}
