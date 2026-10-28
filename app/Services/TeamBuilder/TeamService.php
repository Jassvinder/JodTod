<?php

namespace App\Services\TeamBuilder;

use App\Exceptions\TeamBuilderException;
use App\Models\Contact;
use App\Models\Prospect;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Notifications\TeamMemberJoined;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Team Builder teams, shared by the mobile API and the web app.
 */
class TeamService
{
    public const RULES = [
        'name' => 'required|string|max:100',
        'company_name' => 'nullable|string|max:100',
    ];

    public const JOIN_RULES = [
        'invite_code' => 'required|string|size:8',
    ];

    public function myTeams(int $userId): Collection
    {
        return TeamMember::with(['team', 'sponsor:id,name,avatar,phone'])
            ->where('user_id', $userId)
            ->orderBy('joined_at')
            ->get()
            ->map(fn (TeamMember $member) => $this->summary($member));
    }

    public function membershipOrFail(Team $team, int $userId): TeamMember
    {
        $member = TeamMember::with(['team', 'sponsor:id,name,avatar,phone'])
            ->where('team_id', $team->id)
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            throw new TeamBuilderException('You are not a member of this team.', 403);
        }

        return $member;
    }

    public function ownerOrFail(Team $team, int $userId): void
    {
        if ($team->owner_id !== $userId) {
            throw new TeamBuilderException('Only the team creator can do this.', 403);
        }
    }

    public function create(int $userId, array $data): TeamMember
    {
        $member = DB::transaction(function () use ($userId, $data) {
            $team = Team::create([...$data, 'owner_id' => $userId]);

            return TeamMember::create([
                'team_id' => $team->id,
                'user_id' => $userId,
                'sponsor_id' => null,
                'path' => '/' . $userId . '/',
                'joined_at' => now(),
            ]);
        });

        return $member->load('team');
    }

    /**
     * Join with any member's invite code; that member becomes the sponsor.
     */
    public function join(User $user, string $inviteCode): TeamMember
    {
        $sponsor = TeamMember::with(['team', 'user:id,name'])->where('invite_code', strtoupper($inviteCode))->first();

        if (!$sponsor) {
            throw new TeamBuilderException('Invalid invite code. Ask your sponsor to check the code.', 404);
        }

        if ($sponsor->team->memberFor($user->id)) {
            throw new TeamBuilderException('You are already in this team.');
        }

        $member = DB::transaction(function () use ($sponsor, $user) {
            // Sponsor and new member can reach each other from Contacts right away.
            Contact::connect($user->id, $sponsor->user_id);

            // First team: prospects added before joining belong to it, so the upline can see them.
            if (!TeamMember::where('user_id', $user->id)->exists()) {
                Prospect::where('user_id', $user->id)->whereNull('team_id')->update(['team_id' => $sponsor->team_id]);
            }

            return TeamMember::create([
                'team_id' => $sponsor->team_id,
                'user_id' => $user->id,
                'sponsor_id' => $sponsor->user_id,
                'path' => $sponsor->childPath($user->id),
                'joined_at' => now(),
            ]);
        });

        // Tell the sponsor and everyone above them.
        Notification::send(
            User::whereIn('id', array_diff($member->pathUserIds(), [$user->id]))->get(),
            new TeamMemberJoined($sponsor->team, $user->name, $sponsor->user?->name ?? '', $sponsor->user_id),
        );

        return $member->load(['team', 'sponsor:id,name,avatar,phone']);
    }

    public function leave(Team $team, int $userId): void
    {
        $member = $this->membershipOrFail($team, $userId);

        if ($team->owner_id === $userId) {
            throw new TeamBuilderException('You created this team, so you cannot leave it. Delete it instead.');
        }

        if ($member->directDownline()->exists()) {
            throw new TeamBuilderException('People joined the team under you, so you cannot leave it.');
        }

        $member->delete();
    }

    public function delete(Team $team, int $userId): void
    {
        $this->ownerOrFail($team, $userId);

        if ($team->members()->count() > 1) {
            throw new TeamBuilderException('This team has members, so it cannot be deleted.');
        }

        // Delete explicitly: MyISAM tables ignore the foreign key cascade.
        DB::transaction(function () use ($team) {
            $team->members()->delete();
            $team->delete();
        });
    }

    public function summary(TeamMember $member): array
    {
        $team = $member->team;

        return [
            'id' => $team->id,
            'name' => $team->name,
            'company_name' => $team->company_name,
            'is_owner' => $team->owner_id === $member->user_id,
            'invite_code' => $member->invite_code,
            'joined_at' => $member->joined_at?->toIso8601String(),
            'level' => $member->depth(),
            'sponsor' => $member->sponsor ? [
                'id' => $member->sponsor->id,
                'name' => $member->sponsor->name,
                'avatar_url' => $member->sponsor->avatar_url,
                'phone' => $member->sponsor->phone,
            ] : null,
            'direct_count' => $member->directDownline()->count(),
            'downline_count' => TeamMember::downlineOf($member)->count(),
        ];
    }
}
