<?php

namespace App\Services\TeamBuilder;

use App\Exceptions\TeamBuilderException;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Models\TeamAnnouncement;
use App\Models\TeamBuilderTarget;
use App\Models\TeamMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Team Builder prospects, follow-ups and the personal dashboard, shared by the mobile API and the web app.
 */
class ProspectService
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'source' => 'nullable|string|max:100',
            'team_id' => 'nullable|integer',
            'status' => ['nullable', Rule::in(Prospect::STATUSES)],
            'next_follow_up_at' => 'nullable|date',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function activityRules(): array
    {
        return [
            'type' => ['required', Rule::in(ProspectActivity::TYPES)],
            'note' => 'nullable|string|max:500',
            'done_at' => 'nullable|date|before_or_equal:now',
            'status' => ['nullable', Rule::in(Prospect::STATUSES)],
            'next_follow_up_at' => 'nullable|date',
        ];
    }

    public function authorizeProspect(Prospect $prospect, int $userId): void
    {
        if ($prospect->user_id !== $userId) {
            throw new TeamBuilderException('You do not have access to this prospect.', 403);
        }
    }

    /**
     * Validated prospect data ready to save: the team must be one of the user's teams.
     */
    public function prepare(array $validated, int $userId): array
    {
        if (!empty($validated['team_id'])
            && !TeamMember::where('team_id', $validated['team_id'])->where('user_id', $userId)->exists()) {
            throw new TeamBuilderException('You are not a member of this team.');
        }

        $validated['status'] = $validated['status'] ?? 'new';

        return $validated;
    }

    public function list(int $userId, ?string $status = null, ?string $search = null): array
    {
        $prospects = Prospect::with('team:id,name')
            ->withMax('activities', 'done_at')
            ->where('user_id', $userId)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->orderByDesc('updated_at')
            ->get();

        return [
            'prospects' => $prospects->map(fn (Prospect $prospect) => $prospect->toSummary()),
            'status_counts' => $this->countsByStatus($userId),
        ];
    }

    public function delete(Prospect $prospect): void
    {
        // Delete explicitly: MyISAM tables ignore the foreign key cascade.
        DB::transaction(function () use ($prospect) {
            $prospect->activities()->delete();
            $prospect->delete();
        });
    }

    /**
     * Log a call, plan, follow-up, meeting or note. The same call can move the status and,
     * when $setFollowUp is true, set or clear the next follow-up.
     */
    public function logActivity(Prospect $prospect, array $validated, bool $setFollowUp): Prospect
    {
        DB::transaction(function () use ($prospect, $validated, $setFollowUp) {
            $prospect->activities()->create([
                'type' => $validated['type'],
                'note' => $validated['note'] ?? null,
                'done_at' => $validated['done_at'] ?? now(),
            ]);

            if (!empty($validated['status'])) {
                $prospect->status = $validated['status'];
            } elseif ($validated['type'] === 'plan_shown' && $prospect->status === 'new') {
                $prospect->status = 'plan_shown';
            }

            if ($setFollowUp) {
                $prospect->next_follow_up_at = $validated['next_follow_up_at'] ?? null;
            }

            // Bumped even without other changes so the most recently worked prospect comes first.
            $prospect->updated_at = now();
            $prospect->save();
        });

        return $prospect;
    }

    /**
     * Open prospects with a follow-up date, split into overdue, today and upcoming.
     */
    public function followUps(int $userId): array
    {
        $prospects = Prospect::with('team:id,name')
            ->withMax('activities', 'done_at')
            ->where('user_id', $userId)
            ->open()
            ->whereNotNull('next_follow_up_at')
            ->orderBy('next_follow_up_at')
            ->get();

        $now = now();
        $endOfToday = now()->endOfDay();

        return [
            'overdue' => $prospects->filter(fn ($p) => $p->next_follow_up_at->lt($now))->values()->map->toSummary(),
            'today' => $prospects->filter(fn ($p) => $p->next_follow_up_at->between($now, $endOfToday))->values()->map->toSummary(),
            'upcoming' => $prospects->filter(fn ($p) => $p->next_follow_up_at->gt($endOfToday))->values()->map->toSummary(),
        ];
    }

    /**
     * The user's own numbers for this week (Monday to now), follow-up and pipeline counts,
     * weekly target and the newest announcement from their upline.
     */
    public function dashboard(int $userId): array
    {
        $weekStart = now()->startOfWeek();

        $weekActivities = ProspectActivity::whereHas('prospect', fn ($q) => $q->where('user_id', $userId))
            ->where('done_at', '>=', $weekStart)
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $open = Prospect::where('user_id', $userId)->open()->whereNotNull('next_follow_up_at');

        $teams = TeamMember::with('team:id,name')->where('user_id', $userId)->get();

        $latestAnnouncement = $teams->isEmpty() ? null : TeamAnnouncement::with(['author:id,name,avatar', 'team:id,name'])
            ->where(function ($query) use ($teams) {
                foreach ($teams as $member) {
                    $query->orWhere(fn ($q) => $q->visibleTo($member));
                }
            })
            ->latest()
            ->first();

        return [
            'target' => TeamBuilderTarget::toPayload(TeamBuilderTarget::with('setBy:id,name')->where('user_id', $userId)->first(), $userId),
            'latest_announcement' => $latestAnnouncement?->toPayload($userId),
            'week' => [
                'plans_shown' => (int) ($weekActivities['plan_shown'] ?? 0),
                'follow_ups' => (int) ($weekActivities['follow_up'] ?? 0),
                'calls' => (int) ($weekActivities['call'] ?? 0),
                'meetings' => (int) ($weekActivities['meeting'] ?? 0),
                'new_prospects' => Prospect::where('user_id', $userId)->where('created_at', '>=', $weekStart)->count(),
                'joined' => Prospect::where('user_id', $userId)->where('joined_at', '>=', $weekStart)->count(),
            ],
            'follow_ups' => [
                'overdue' => (clone $open)->where('next_follow_up_at', '<', now())->count(),
                'today' => (clone $open)->whereBetween('next_follow_up_at', [now(), now()->endOfDay()])->count(),
            ],
            'pipeline' => $this->countsByStatus($userId),
            'teams' => $teams->map(fn (TeamMember $member) => [
                'id' => $member->team_id,
                'name' => $member->team?->name,
                'downline_count' => TeamMember::downlineOf($member)->count(),
            ]),
        ];
    }

    public function saveMyTarget(int $userId, array $validated): TeamBuilderTarget
    {
        return TeamBuilderTarget::updateOrCreate(['user_id' => $userId], [...$validated, 'set_by' => $userId]);
    }

    private function countsByStatus(int $userId)
    {
        $counts = Prospect::where('user_id', $userId)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(Prospect::STATUSES)->mapWithKeys(fn ($status) => [$status => (int) ($counts[$status] ?? 0)]);
    }
}
