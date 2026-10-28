<?php

namespace App\Http\Controllers\TeamBuilder;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Services\TeamBuilder\ProspectService;
use App\Services\TeamBuilder\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Team Builder prospects (web): list and selected prospect on one page, like Remember.
 */
class ProspectController extends Controller
{
    public function __construct(
        private ProspectService $prospects,
        private TeamService $teams,
    ) {}

    public function index()
    {
        return $this->render();
    }

    public function show(Prospect $prospect)
    {
        $this->prospects->authorizeProspect($prospect, Auth::id());

        return $this->render($prospect);
    }

    public function store(Request $request)
    {
        $prospect = Prospect::create([
            ...$this->prospects->prepare($request->validate($this->prospects->rules()), Auth::id()),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('team-builder.prospects.show', $prospect)->with('success', 'Prospect added.');
    }

    public function update(Request $request, Prospect $prospect)
    {
        $this->prospects->authorizeProspect($prospect, Auth::id());

        $prospect->update($this->prospects->prepare($request->validate($this->prospects->rules()), Auth::id()));

        return back()->with('success', 'Prospect updated.');
    }

    public function destroy(Prospect $prospect)
    {
        $this->prospects->authorizeProspect($prospect, Auth::id());

        $this->prospects->delete($prospect);

        return redirect()->route('team-builder.prospects.index')->with('success', 'Prospect deleted.');
    }

    public function storeActivity(Request $request, Prospect $prospect)
    {
        $this->prospects->authorizeProspect($prospect, Auth::id());

        // The web form always sends the follow-up field; empty means no follow-up planned.
        $this->prospects->logActivity($prospect, $request->validate($this->prospects->activityRules()), true);

        return back()->with('success', 'Activity saved.');
    }

    public function destroyActivity(Prospect $prospect, ProspectActivity $activity)
    {
        $this->prospects->authorizeProspect($prospect, Auth::id());

        if ($activity->prospect_id !== $prospect->id) {
            abort(404);
        }

        $activity->delete();

        return back()->with('success', 'Activity deleted.');
    }

    private function render(?Prospect $selected = null)
    {
        return Inertia::render('TeamBuilder/Prospects', [
            ...$this->prospects->list(Auth::id()),
            'selected' => $selected?->load('team:id,name')->toDetail(),
            'teams' => $this->teams->myTeams(Auth::id())->map(fn ($team) => ['id' => $team['id'], 'name' => $team['name']]),
        ]);
    }
}
