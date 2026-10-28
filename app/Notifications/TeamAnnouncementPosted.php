<?php

namespace App\Notifications;

use App\Models\TeamAnnouncement;
use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TeamAnnouncementPosted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private TeamAnnouncement $announcement,
        private string $authorName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', ExpoPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'team_announcement',
            'team_id' => $this->announcement->team_id,
            'message' => "{$this->authorName}: " . Str::limit($this->announcement->message, 120),
        ];
    }
}
