<?php

namespace App\Notifications;

use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Monday team update for anyone with people below them in a team.
 */
class TeamWeeklySummary extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $teamId,
        private string $teamName,
        private array $counts,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $parts = [$this->counts['total'] . ($this->counts['total'] === 1 ? ' person' : ' people')];
        if ($this->counts['push'] > 0) {
            $parts[] = "{$this->counts['push']} need a push";
        }
        if ($this->counts['training'] > 0) {
            $parts[] = "{$this->counts['training']} need training";
        }
        if ($this->counts['overdue'] > 0) {
            $parts[] = "{$this->counts['overdue']} follow-ups overdue";
        }

        return [
            'type' => 'team_weekly_summary',
            'team_id' => $this->teamId,
            'message' => "{$this->teamName} this week: " . implode(', ', $parts) . '.',
        ];
    }
}
