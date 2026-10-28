<?php

namespace App\Http\Controllers\TeamBuilder;

use App\Http\Controllers\Controller;
use App\Models\TeamBuilderTarget;
use App\Services\TeamBuilder\ProspectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Team Builder (web): my weekly numbers, follow-up alerts, target and the newest team announcement.
 */
class DashboardController extends Controller
{
    public function __construct(private ProspectService $prospects) {}

    public function index()
    {
        return Inertia::render('TeamBuilder/Dashboard', [
            'dashboard' => $this->prospects->dashboard(Auth::id()),
        ]);
    }

    public function followUps()
    {
        return Inertia::render('TeamBuilder/FollowUps', [
            'followUps' => $this->prospects->followUps(Auth::id()),
        ]);
    }

    public function updateTarget(Request $request)
    {
        $this->prospects->saveMyTarget(Auth::id(), $request->validate(TeamBuilderTarget::RULES));

        return back()->with('success', 'Weekly target saved.');
    }
}
