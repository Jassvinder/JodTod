<?php

namespace App\Http\Controllers\Api\V1\TeamBuilder;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Models\TeamBuilderTarget;
use App\Services\TeamBuilder\ProspectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Team Builder prospects: people the user shows the plan to, their status and follow-ups.
 * Rules live in ProspectService (shared with the web app).
 */
class ProspectController extends Controller
{
    public function __construct(private ProspectService $prospects) {}

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', Rule::in(Prospect::STATUSES)],
            'search' => 'nullable|string|max:100',
        ]);

        return $this->success($this->prospects->list(Auth::id(), $request->status, $request->search));
    }

    public function show(Prospect $prospect): JsonResponse
    {
        $this->prospects->authorizeProspect($prospect, Auth::id());

        return $this->success($prospect->load('team:id,name')->toDetail());
    }

    public function store(Request $request): JsonResponse
    {
        $prospect = Prospect::create([
            ...$this->prospects->prepare($request->validate($this->prospects->rules()), Auth::id()),
            'user_id' => Auth::id(),
        ]);

        return $this->created($prospect->load('team:id,name')->toDetail(), 'Prospect added successfully.');
    }

    public function update(Request $request, Prospect $prospect): JsonResponse
    {
        $this->prospects->authorizeProspect($prospect, Auth::id());

        $prospect->update($this->prospects->prepare($request->validate($this->prospects->rules()), Auth::id()));

        return $this->success($prospect->load('team:id,name')->toDetail(), 'Prospect updated successfully.');
    }

    public function destroy(Prospect $prospect): JsonResponse
    {
        $this->prospects->authorizeProspect($prospect, Auth::id());

        $this->prospects->delete($prospect);

        return $this->success(null, 'Prospect deleted successfully.');
    }

    public function storeActivity(Request $request, Prospect $prospect): JsonResponse
    {
        $this->prospects->authorizeProspect($prospect, Auth::id());

        $validated = $request->validate($this->prospects->activityRules());
        // Sending the key with null clears the follow-up; leaving it out keeps it.
        $this->prospects->logActivity($prospect, $validated, $request->exists('next_follow_up_at'));

        return $this->created($prospect->load('team:id,name')->toDetail(), 'Activity saved.');
    }

    public function destroyActivity(Prospect $prospect, ProspectActivity $activity): JsonResponse
    {
        $this->prospects->authorizeProspect($prospect, Auth::id());

        if ($activity->prospect_id !== $prospect->id) {
            return $this->notFound('Activity not found.');
        }

        $activity->delete();

        return $this->success($prospect->load('team:id,name')->toDetail(), 'Activity deleted.');
    }

    public function followUps(): JsonResponse
    {
        return $this->success($this->prospects->followUps(Auth::id()));
    }

    public function dashboard(): JsonResponse
    {
        return $this->success($this->prospects->dashboard(Auth::id()));
    }

    /**
     * The user sets their own weekly target (shown on the dashboard with progress).
     */
    public function updateMyTarget(Request $request): JsonResponse
    {
        $target = $this->prospects->saveMyTarget(Auth::id(), $request->validate(TeamBuilderTarget::RULES));

        return $this->success(TeamBuilderTarget::toPayload($target, Auth::id()), 'Weekly target saved.');
    }
}
