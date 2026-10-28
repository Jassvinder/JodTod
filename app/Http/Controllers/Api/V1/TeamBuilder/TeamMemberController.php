<?php

namespace App\Http\Controllers\Api\V1\TeamBuilder;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamBuilderTarget;
use App\Services\TeamBuilder\TeamService;
use App\Services\TeamBuilder\TeamStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Upline view: everyone below me in a team as one flat list, with their follow-up status and a
 * simple flag (needs a push / needs training). Uplines see prospect names, statuses and dates,
 * never phone numbers or notes.
 */
class TeamMemberController extends Controller
{
    public function __construct(
        private TeamService $teams,
        private TeamStatsService $stats,
    ) {}

    public function index(Team $team): JsonResponse
    {
        $list = $this->stats->downlineSummaries($this->teams->membershipOrFail($team, Auth::id()));

        return $this->success([
            'members' => $list,
            'counts' => [
                'total' => $list->count(),
                'push' => $list->where('flag', 'push')->count(),
                'training' => $list->where('flag', 'training')->count(),
            ],
        ]);
    }

    public function show(Team $team, int $userId): JsonResponse
    {
        return $this->success($this->stats->memberDetail($this->teams->membershipOrFail($team, Auth::id()), $userId));
    }

    public function updateTarget(Request $request, Team $team, int $userId): JsonResponse
    {
        $me = $this->teams->membershipOrFail($team, Auth::id());
        $summary = $this->stats->setMemberTarget($me, $request->user(), $userId, $request->validate(TeamBuilderTarget::RULES));

        return $this->success($summary, "Weekly target set for {$summary['name']}.");
    }
}
