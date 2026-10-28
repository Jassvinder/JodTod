<?php

namespace App\Http\Controllers\Api\V1\TeamBuilder;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\TeamBuilder\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Team Builder teams: create, join with a member's invite code (that member becomes the sponsor),
 * and view your place in the team. Rules live in TeamService (shared with the web app).
 */
class TeamController extends Controller
{
    public function __construct(private TeamService $teams) {}

    public function index(): JsonResponse
    {
        return $this->success($this->teams->myTeams(Auth::id()));
    }

    public function show(Team $team): JsonResponse
    {
        // The people under me are listed by TeamMemberController::index.
        return $this->success($this->teams->summary($this->teams->membershipOrFail($team, Auth::id())));
    }

    public function store(Request $request): JsonResponse
    {
        $member = $this->teams->create(Auth::id(), $request->validate(TeamService::RULES));

        return $this->created($this->teams->summary($member), 'Team created successfully.');
    }

    public function update(Request $request, Team $team): JsonResponse
    {
        $this->teams->ownerOrFail($team, Auth::id());

        $team->update($request->validate(TeamService::RULES));

        return $this->success($this->teams->summary($this->teams->membershipOrFail($team, Auth::id())), 'Team updated successfully.');
    }

    public function destroy(Team $team): JsonResponse
    {
        $this->teams->delete($team, Auth::id());

        return $this->success(null, 'Team deleted successfully.');
    }

    public function join(Request $request): JsonResponse
    {
        $validated = $request->validate(TeamService::JOIN_RULES);

        $member = $this->teams->join($request->user(), $validated['invite_code']);

        return $this->created($this->teams->summary($member), 'You joined the team.');
    }

    public function leave(Team $team): JsonResponse
    {
        $this->teams->leave($team, Auth::id());

        return $this->success(null, 'You left the team.');
    }
}
