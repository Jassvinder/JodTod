<?php

namespace App\Http\Controllers\TeamBuilder;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamAnnouncement;
use App\Models\TeamBuilderTarget;
use App\Services\TeamBuilder\AnnouncementService;
use App\Services\TeamBuilder\TeamService;
use App\Services\TeamBuilder\TeamStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Team Builder teams (web): my teams, one team with everyone below me and its announcements,
 * and one member's numbers and prospects.
 */
class TeamController extends Controller
{
    public function __construct(
        private TeamService $teams,
        private TeamStatsService $stats,
        private AnnouncementService $announcements,
    ) {}

    public function index()
    {
        return Inertia::render('TeamBuilder/Teams', [
            'teams' => $this->teams->myTeams(Auth::id()),
        ]);
    }

    public function show(Team $team)
    {
        $me = $this->teams->membershipOrFail($team, Auth::id());

        return Inertia::render('TeamBuilder/TeamShow', [
            'team' => $this->teams->summary($me),
            'members' => $this->stats->downlineSummaries($me),
            ...$this->announcements->list($me),
        ]);
    }

    public function memberShow(Team $team, int $userId)
    {
        $me = $this->teams->membershipOrFail($team, Auth::id());

        return Inertia::render('TeamBuilder/Member', [
            'team' => ['id' => $team->id, 'name' => $team->name],
            'member' => $this->stats->memberDetail($me, $userId),
        ]);
    }

    public function store(Request $request)
    {
        $member = $this->teams->create(Auth::id(), $request->validate(TeamService::RULES));

        return redirect()->route('team-builder.teams.show', $member->team_id)->with('success', 'Team created. Share your invite code to build it.');
    }

    public function join(Request $request)
    {
        $validated = $request->validate(TeamService::JOIN_RULES);

        $member = $this->teams->join($request->user(), $validated['invite_code']);

        return redirect()->route('team-builder.teams.show', $member->team_id)->with('success', 'You joined the team.');
    }

    public function update(Request $request, Team $team)
    {
        $this->teams->ownerOrFail($team, Auth::id());

        $team->update($request->validate(TeamService::RULES));

        return back()->with('success', 'Team updated.');
    }

    public function destroy(Team $team)
    {
        $this->teams->delete($team, Auth::id());

        return redirect()->route('team-builder.teams.index')->with('success', 'Team deleted.');
    }

    public function leave(Team $team)
    {
        $this->teams->leave($team, Auth::id());

        return redirect()->route('team-builder.teams.index')->with('success', 'You left the team.');
    }

    public function updateMemberTarget(Request $request, Team $team, int $userId)
    {
        $me = $this->teams->membershipOrFail($team, Auth::id());
        $summary = $this->stats->setMemberTarget($me, $request->user(), $userId, $request->validate(TeamBuilderTarget::RULES));

        return back()->with('success', "Weekly target set for {$summary['name']}.");
    }

    public function storeAnnouncement(Request $request, Team $team)
    {
        $me = $this->teams->membershipOrFail($team, Auth::id());

        [, $count] = $this->announcements->post($me, $request->user(), $request->validate(AnnouncementService::RULES));

        return back()->with('success', "Sent to {$count} " . ($count === 1 ? 'person' : 'people') . '.');
    }

    public function destroyAnnouncement(TeamAnnouncement $announcement)
    {
        $this->announcements->delete($announcement, Auth::id());

        return back()->with('success', 'Announcement deleted.');
    }
}
