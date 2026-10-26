<?php

namespace App\Http\Controllers\Api\V1\TeamBuilder;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Team Builder prospects: people the user shows the plan to, their status and follow-ups.
 */
class ProspectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', Rule::in(Prospect::STATUSES)],
            'search' => 'nullable|string|max:100',
        ]);

        $prospects = Prospect::with('team:id,name')
            ->withMax('activities', 'done_at')
            ->where('user_id', Auth::id())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->orderByDesc('updated_at')
            ->get();

        $counts = Prospect::where('user_id', Auth::id())
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return $this->success([
            'prospects' => $prospects->map(fn (Prospect $prospect) => $prospect->toSummary()),
            'status_counts' => collect(Prospect::STATUSES)->mapWithKeys(fn ($status) => [$status => (int) ($counts[$status] ?? 0)]),
        ]);
    }

    public function show(Prospect $prospect): JsonResponse
    {
        $this->authorizeProspect($prospect);

        return $this->success($prospect->load('team:id,name')->toDetail());
    }

    public function store(Request $request): JsonResponse
    {
        $prospect = Prospect::create([
            ...$this->validateProspect($request),
            'user_id' => Auth::id(),
        ]);

        return $this->created($prospect->load('team:id,name')->toDetail(), 'Prospect added successfully.');
    }

    public function update(Request $request, Prospect $prospect): JsonResponse
    {
        $this->authorizeProspect($prospect);

        $prospect->update($this->validateProspect($request));

        return $this->success($prospect->load('team:id,name')->toDetail(), 'Prospect updated successfully.');
    }

    public function destroy(Prospect $prospect): JsonResponse
    {
        $this->authorizeProspect($prospect);

        // Delete explicitly: MyISAM tables ignore the foreign key cascade.
        DB::transaction(function () use ($prospect) {
            $prospect->activities()->delete();
            $prospect->delete();
        });

        return $this->success(null, 'Prospect deleted successfully.');
    }

    /**
     * Log a call, plan, follow-up, meeting or note. The same request can move the status
     * and set (or clear) the next follow-up.
     */
    public function storeActivity(Request $request, Prospect $prospect): JsonResponse
    {
        $this->authorizeProspect($prospect);

        $validated = $request->validate([
            'type' => ['required', Rule::in(ProspectActivity::TYPES)],
            'note' => 'nullable|string|max:500',
            'done_at' => 'nullable|date|before_or_equal:now',
            'status' => ['nullable', Rule::in(Prospect::STATUSES)],
            'next_follow_up_at' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, $prospect, $validated) {
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

            if ($request->exists('next_follow_up_at')) {
                $prospect->next_follow_up_at = $validated['next_follow_up_at'];
            }

            // Bumped even without other changes so the most recently worked prospect comes first.
            $prospect->updated_at = now();
            $prospect->save();
        });

        return $this->created($prospect->load('team:id,name')->toDetail(), 'Activity saved.');
    }

    public function destroyActivity(Prospect $prospect, ProspectActivity $activity): JsonResponse
    {
        $this->authorizeProspect($prospect);

        if ($activity->prospect_id !== $prospect->id) {
            return $this->notFound('Activity not found.');
        }

        $activity->delete();

        return $this->success($prospect->load('team:id,name')->toDetail(), 'Activity deleted.');
    }

    /**
     * Open prospects with a follow-up date, split into overdue, today and upcoming.
     */
    public function followUps(): JsonResponse
    {
        $prospects = Prospect::with('team:id,name')
            ->withMax('activities', 'done_at')
            ->where('user_id', Auth::id())
            ->open()
            ->whereNotNull('next_follow_up_at')
            ->orderBy('next_follow_up_at')
            ->get();

        $now = now();
        $endOfToday = now()->endOfDay();

        return $this->success([
            'overdue' => $prospects->filter(fn ($p) => $p->next_follow_up_at->lt($now))->values()->map->toSummary(),
            'today' => $prospects->filter(fn ($p) => $p->next_follow_up_at->between($now, $endOfToday))->values()->map->toSummary(),
            'upcoming' => $prospects->filter(fn ($p) => $p->next_follow_up_at->gt($endOfToday))->values()->map->toSummary(),
        ]);
    }

    /**
     * The user's own numbers for this week (Monday to now), plus follow-up and pipeline counts.
     */
    public function dashboard(): JsonResponse
    {
        $userId = Auth::id();
        $weekStart = now()->startOfWeek();

        $weekActivities = ProspectActivity::whereHas('prospect', fn ($q) => $q->where('user_id', $userId))
            ->where('done_at', '>=', $weekStart)
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $open = Prospect::where('user_id', $userId)->open()->whereNotNull('next_follow_up_at');

        $pipeline = Prospect::where('user_id', $userId)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $teams = TeamMember::with('team:id,name')->where('user_id', $userId)->get();

        return $this->success([
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
            'pipeline' => collect(Prospect::STATUSES)->mapWithKeys(fn ($status) => [$status => (int) ($pipeline[$status] ?? 0)]),
            'teams' => $teams->map(fn (TeamMember $member) => [
                'id' => $member->team_id,
                'name' => $member->team?->name,
                'downline_count' => TeamMember::downlineOf($member)->count(),
            ]),
        ]);
    }

    private function validateProspect(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'source' => 'nullable|string|max:100',
            'team_id' => 'nullable|integer',
            'status' => ['nullable', Rule::in(Prospect::STATUSES)],
            'next_follow_up_at' => 'nullable|date',
            'note' => 'nullable|string|max:500',
        ]);

        if (!empty($validated['team_id'])
            && !TeamMember::where('team_id', $validated['team_id'])->where('user_id', Auth::id())->exists()) {
            abort(response()->json([
                'success' => false,
                'message' => 'You are not a member of this team.',
                'errors' => ['team_id' => ['You are not a member of this team.']],
            ], 422));
        }

        $validated['status'] = $validated['status'] ?? 'new';

        return $validated;
    }

    private function authorizeProspect(Prospect $prospect): void
    {
        if ($prospect->user_id !== Auth::id()) {
            abort(response()->json([
                'success' => false,
                'message' => 'You do not have access to this prospect.',
            ], 403));
        }
    }
}
