<?php

namespace App\Services\TeamBuilder;

use App\Exceptions\TeamBuilderException;
use App\Models\TeamAnnouncement;
use App\Models\TeamMember;
use App\Models\User;
use App\Notifications\TeamAnnouncementPosted;
use Illuminate\Support\Facades\Notification;

/**
 * Messages from a member to everyone below them (meeting times, training links).
 * A member reads what they and their upline posted in the team.
 */
class AnnouncementService
{
    public const RULES = [
        'message' => 'required|string|max:1000',
        'link' => 'nullable|url|max:500',
    ];

    public function list(TeamMember $me): array
    {
        return [
            'announcements' => TeamAnnouncement::visibleTo($me)
                ->with(['author:id,name,avatar', 'team:id,name'])
                ->latest()
                ->limit(50)
                ->get()
                ->map(fn (TeamAnnouncement $announcement) => $announcement->toPayload($me->user_id)),
            // Only people with someone below them have an audience.
            'can_post' => TeamMember::downlineOf($me)->exists(),
        ];
    }

    /**
     * Post and push it to everyone below the author. Returns the announcement and how many people got it.
     *
     * @return array{0: TeamAnnouncement, 1: int}
     */
    public function post(TeamMember $me, User $author, array $validated): array
    {
        $recipients = TeamMember::downlineOf($me)->with('user')->get()->pluck('user')->filter();

        if ($recipients->isEmpty()) {
            throw new TeamBuilderException('Nobody has joined under you yet, so there is nobody to send this to.');
        }

        $announcement = TeamAnnouncement::create([
            ...$validated,
            'team_id' => $me->team_id,
            'user_id' => $author->id,
        ]);

        Notification::send($recipients, new TeamAnnouncementPosted($announcement, $author->name));

        return [$announcement->load(['author:id,name,avatar', 'team:id,name']), $recipients->count()];
    }

    public function delete(TeamAnnouncement $announcement, int $userId): void
    {
        if ($announcement->user_id !== $userId) {
            throw new TeamBuilderException('You can only delete your own announcements.', 403);
        }

        $announcement->delete();
    }
}
