<?php

namespace App\Http\Controllers\Api\V1\TeamBuilder;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamAnnouncement;
use App\Services\TeamBuilder\AnnouncementService;
use App\Services\TeamBuilder\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamAnnouncementController extends Controller
{
    public function __construct(
        private TeamService $teams,
        private AnnouncementService $announcements,
    ) {}

    public function index(Team $team): JsonResponse
    {
        return $this->success($this->announcements->list($this->teams->membershipOrFail($team, Auth::id())));
    }

    public function store(Request $request, Team $team): JsonResponse
    {
        $me = $this->teams->membershipOrFail($team, Auth::id());

        [$announcement, $count] = $this->announcements->post($me, $request->user(), $request->validate(AnnouncementService::RULES));

        return $this->created(
            $announcement->toPayload(Auth::id()),
            "Sent to {$count} " . ($count === 1 ? 'person' : 'people') . '.',
        );
    }

    public function destroy(TeamAnnouncement $announcement): JsonResponse
    {
        $this->announcements->delete($announcement, Auth::id());

        return $this->success(null, 'Announcement deleted.');
    }
}
