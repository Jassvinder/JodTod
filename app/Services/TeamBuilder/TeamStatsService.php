<?php

namespace App\Services\TeamBuilder;

use App\Exceptions\TeamBuilderException;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Models\Team;
use App\Models\TeamBuilderTarget;
use App\Models\TeamMember;
use App\Models\User;
use App\Notifications\TeamTargetSet;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Numbers an upline sees for the people below them in a team. Only prospects saved under the team count.
 */
class TeamStatsService
{
    // No call, plan, follow-up or meeting logged for this many days: "Push needed".
    public const PUSH_AFTER_DAYS = 7;

    // Showed at least this many plans in the window but nobody joined: "Training needed".
    public const TRAINING_WINDOW_DAYS = 30;
    public const TRAINING_MIN_PLANS = 5;

    /**
     * Everyone below $me as one flat list, members who need attention first.
     */
    public function downlineSummaries(TeamMember $me): Collection
    {
        $members = TeamMember::downlineOf($me)
            ->with(['user:id,name,avatar,phone', 'sponsor:id,name'])
            ->get();

        return $this->summaries($me->team, $me, $members)
            ->sortBy([
                fn ($a, $b) => ($a['flag'] === null) <=> ($b['flag'] === null),
                fn ($a, $b) => $b['overdue_follow_ups'] <=> $a['overdue_follow_ups'],
                fn ($a, $b) => strcmp($a['name'] ?? '', $b['name'] ?? ''),
            ])
            ->values();
    }

    public function downlineMemberOrFail(TeamMember $me, int $userId): TeamMember
    {
        $member = TeamMember::downlineOf($me)
            ->with(['user:id,name,avatar,phone', 'sponsor:id,name'])
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            throw new TeamBuilderException('This person is not in your team.', 404);
        }

        return $member;
    }

    /**
     * Member summary plus their prospects for this team: names, statuses and dates only (no phone or note).
     */
    public function memberDetail(TeamMember $me, int $userId): array
    {
        $member = $this->downlineMemberOrFail($me, $userId);

        $prospects = Prospect::where('team_id', $me->team_id)
            ->where('user_id', $userId)
            ->withMax('activities', 'done_at')
            ->get()
            // Open prospects first, soonest follow-up first; closed ones after, newest first.
            ->sortBy([
                fn ($a, $b) => !in_array($a->status, Prospect::OPEN_STATUSES, true) <=> !in_array($b->status, Prospect::OPEN_STATUSES, true),
                fn ($a, $b) => ($a->next_follow_up_at?->timestamp ?? PHP_INT_MAX) <=> ($b->next_follow_up_at?->timestamp ?? PHP_INT_MAX),
                fn ($a, $b) => $b->updated_at <=> $a->updated_at,
            ])
            ->values()
            ->map(fn (Prospect $prospect) => [
                'id' => $prospect->id,
                'name' => $prospect->name,
                'status' => $prospect->status,
                'next_follow_up_at' => $prospect->next_follow_up_at?->toIso8601String(),
                'joined_at' => $prospect->joined_at?->toIso8601String(),
                'last_activity_at' => $prospect->toSummary()['last_activity_at'],
            ]);

        return [
            ...$this->summaries($me->team, $me, collect([$member]))->first(),
            'prospects' => $prospects,
        ];
    }

    /**
     * An upline sets the weekly target of someone below them; the member is notified.
     */
    public function setMemberTarget(TeamMember $me, User $setBy, int $userId, array $validated): array
    {
        $member = $this->downlineMemberOrFail($me, $userId);

        $target = TeamBuilderTarget::updateOrCreate(['user_id' => $userId], [...$validated, 'set_by' => $setBy->id]);

        $member->user?->notify(new TeamTargetSet($me->team, $setBy->name, $target));

        return $this->summaries($me->team, $me, collect([$member]))->first();
    }

    /**
     * @param Collection<int, TeamMember> $members
     */
    public function summaries(Team $team, TeamMember $me, Collection $members): Collection
    {
        $userIds = $members->pluck('user_id');
        $stats = $this->statsFor($team, $userIds);
        $targets = TeamBuilderTarget::with('setBy:id,name')->whereIn('user_id', $userIds)->get()->keyBy('user_id');

        return $members->map(fn (TeamMember $member) => $this->summary(
            $member,
            $me,
            $stats[$member->user_id] ?? [],
            $targets[$member->user_id] ?? null,
        ));
    }

    private function summary(TeamMember $member, TeamMember $me, array $stats, ?TeamBuilderTarget $target): array
    {
        $lastActive = isset($stats['last_active_at']) ? Carbon::parse($stats['last_active_at']) : null;

        // New members get the same grace period from the day they joined.
        $activeSince = $lastActive && $lastActive->gt($member->joined_at) ? $lastActive : $member->joined_at;

        $flag = null;
        if ($activeSince->lt(now()->subDays(self::PUSH_AFTER_DAYS))) {
            $flag = 'push';
        } elseif (($stats['plans_recent'] ?? 0) >= self::TRAINING_MIN_PLANS && ($stats['joined_recent'] ?? 0) === 0) {
            $flag = 'training';
        }

        return [
            'user_id' => $member->user_id,
            'name' => $member->user?->name,
            'avatar_url' => $member->user?->avatar_url,
            'phone' => $member->user?->phone,
            'joined_at' => $member->joined_at?->toIso8601String(),
            'is_direct' => $member->sponsor_id === $me->user_id,
            'sponsor_name' => $member->sponsor?->name,
            'last_active_at' => $lastActive?->toIso8601String(),
            'open_prospects' => $stats['open_prospects'] ?? 0,
            'overdue_follow_ups' => $stats['overdue_follow_ups'] ?? 0,
            'plans_week' => $stats['plans_week'] ?? 0,
            'follow_ups_week' => $stats['follow_ups_week'] ?? 0,
            'joined_week' => $stats['joined_week'] ?? 0,
            'target' => TeamBuilderTarget::toPayload($target, $member->user_id),
            'flag' => $flag,
        ];
    }

    /**
     * Per-user numbers for prospects saved under this team, keyed by user id.
     */
    private function statsFor(Team $team, Collection $userIds): array
    {
        if ($userIds->isEmpty()) {
            return [];
        }

        $weekStart = now()->startOfWeek();
        $windowStart = now()->subDays(self::TRAINING_WINDOW_DAYS);
        $stats = [];

        $prospectRows = Prospect::where('team_id', $team->id)
            ->whereIn('user_id', $userIds)
            ->select('user_id')
            ->selectRaw('SUM(status IN (?, ?, ?)) as open_prospects', Prospect::OPEN_STATUSES)
            ->selectRaw('SUM(status IN (?, ?, ?) AND next_follow_up_at < ?) as overdue_follow_ups', [...Prospect::OPEN_STATUSES, now()])
            ->selectRaw('SUM(joined_at >= ?) as joined_recent', [$windowStart])
            ->selectRaw('SUM(joined_at >= ?) as joined_week', [$weekStart])
            ->groupBy('user_id')
            ->get();

        foreach ($prospectRows as $row) {
            $stats[$row->user_id] = [
                'open_prospects' => (int) $row->open_prospects,
                'overdue_follow_ups' => (int) $row->overdue_follow_ups,
                'joined_recent' => (int) $row->joined_recent,
                'joined_week' => (int) $row->joined_week,
            ];
        }

        $activityRows = ProspectActivity::join('prospects', 'prospects.id', '=', 'prospect_activities.prospect_id')
            ->where('prospects.team_id', $team->id)
            ->whereIn('prospects.user_id', $userIds)
            ->select('prospects.user_id')
            ->selectRaw('MAX(prospect_activities.done_at) as last_active_at')
            ->selectRaw("SUM(prospect_activities.type = 'plan_shown' AND prospect_activities.done_at >= ?) as plans_week", [$weekStart])
            ->selectRaw("SUM(prospect_activities.type = 'follow_up' AND prospect_activities.done_at >= ?) as follow_ups_week", [$weekStart])
            ->selectRaw("SUM(prospect_activities.type = 'plan_shown' AND prospect_activities.done_at >= ?) as plans_recent", [$windowStart])
            ->groupBy('prospects.user_id')
            ->get();

        foreach ($activityRows as $row) {
            $stats[$row->user_id] = [
                ...($stats[$row->user_id] ?? []),
                'last_active_at' => $row->last_active_at,
                'plans_week' => (int) $row->plans_week,
                'follow_ups_week' => (int) $row->follow_ups_week,
                'plans_recent' => (int) $row->plans_recent,
            ];
        }

        return $stats;
    }
}
