<?php

namespace App\Http\Controllers\Api\V1\TeamBuilder;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Team Builder teams: create, join with a member's invite code (that member becomes the sponsor),
 * and view your place in the upline/downline tree.
 */
class TeamController extends Controller
{
    public function index(): JsonResponse
    {
        $memberships = TeamMember::with(['team', 'sponsor:id,name,avatar,phone'])
            ->where('user_id', Auth::id())
            ->orderBy('joined_at')
            ->get();

        return $this->success($memberships->map(fn (TeamMember $member) => $this->teamSummary($member)));
    }

    public function show(Team $team): JsonResponse
    {
        $member = $this->membershipOrFail($team);

        $directs = $member->directDownline()
            ->with('user:id,name,avatar,phone')
            ->orderBy('joined_at')
            ->get()
            ->map(fn (TeamMember $direct) => [
                'user_id' => $direct->user_id,
                'name' => $direct->user?->name,
                'avatar_url' => $direct->user?->avatar_url,
                'phone' => $direct->user?->phone,
                'joined_at' => $direct->joined_at?->toIso8601String(),
                'direct_count' => $direct->directDownline()->count(),
                'downline_count' => TeamMember::downlineOf($direct)->count(),
            ]);

        return $this->success([
            ...$this->teamSummary($member),
            'direct_members' => $directs,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:100',
        ]);

        $member = DB::transaction(function () use ($validated) {
            $team = Team::create([...$validated, 'owner_id' => Auth::id()]);

            return TeamMember::create([
                'team_id' => $team->id,
                'user_id' => Auth::id(),
                'sponsor_id' => null,
                'path' => '/' . Auth::id() . '/',
                'joined_at' => now(),
            ]);
        });

        return $this->created($this->teamSummary($member->load('team')), 'Team created successfully.');
    }

    public function update(Request $request, Team $team): JsonResponse
    {
        $this->ownerOrFail($team);

        $team->update($request->validate([
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:100',
        ]));

        return $this->success($this->teamSummary($this->membershipOrFail($team)), 'Team updated successfully.');
    }

    public function destroy(Team $team): JsonResponse
    {
        $this->ownerOrFail($team);

        if ($team->members()->count() > 1) {
            return $this->error('This team has members, so it cannot be deleted.', 422);
        }

        // Delete explicitly: MyISAM tables ignore the foreign key cascade.
        DB::transaction(function () use ($team) {
            $team->members()->delete();
            $team->delete();
        });

        return $this->success(null, 'Team deleted successfully.');
    }

    public function join(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invite_code' => 'required|string|size:8',
        ]);

        $sponsor = TeamMember::with('team')->where('invite_code', strtoupper($validated['invite_code']))->first();

        if (!$sponsor) {
            return $this->error('Invalid invite code. Ask your sponsor to check the code.', 404);
        }

        if ($sponsor->team->memberFor(Auth::id())) {
            return $this->error('You are already in this team.', 422);
        }

        $member = TeamMember::create([
            'team_id' => $sponsor->team_id,
            'user_id' => Auth::id(),
            'sponsor_id' => $sponsor->user_id,
            'path' => $sponsor->childPath(Auth::id()),
            'joined_at' => now(),
        ]);

        return $this->created($this->teamSummary($member->load(['team', 'sponsor:id,name,avatar,phone'])), 'You joined the team.');
    }

    public function leave(Team $team): JsonResponse
    {
        $member = $this->membershipOrFail($team);

        if ($team->owner_id === Auth::id()) {
            return $this->error('You created this team, so you cannot leave it. Delete it instead.', 422);
        }

        if ($member->directDownline()->exists()) {
            return $this->error('People joined the team under you, so you cannot leave it.', 422);
        }

        $member->delete();

        return $this->success(null, 'You left the team.');
    }

    private function teamSummary(TeamMember $member): array
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

    private function membershipOrFail(Team $team): TeamMember
    {
        $member = TeamMember::with(['team', 'sponsor:id,name,avatar,phone'])
            ->where('team_id', $team->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$member) {
            abort(response()->json([
                'success' => false,
                'message' => 'You are not a member of this team.',
            ], 403));
        }

        return $member;
    }

    private function ownerOrFail(Team $team): void
    {
        if ($team->owner_id !== Auth::id()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Only the team creator can do this.',
            ], 403));
        }
    }
}
