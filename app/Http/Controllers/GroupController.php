<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Notifications\AddedToGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    public function index(Request $request): Response
    {
        $groups = $request->user()
            ->groups()
            ->withCount('members')
            ->withPivot('role')
            ->latest()
            ->get();

        return Inertia::render('Groups/Index', [
            'groups' => $groups,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Groups/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $group = Group::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        // Creator becomes admin
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
            'role' => 'admin',
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group created successfully.');
    }

    public function show(Request $request, Group $group): Response
    {
        if (!$group->isMember($request->user())) {
            abort(403);
        }

        $group->load(['members' => function ($query) {
            $query->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.avatar')
                ->orderByPivot('joined_at');
        }, 'creator:id,name']);

        // Get recent expenses for the group (last 5)
        $recentExpenses = $group->expenses()
            ->with(['category', 'payer'])
            ->whereNull('deleted_at')
            ->orderBy('expense_date', 'desc')
            ->limit(5)
            ->get();

        // Total expenses count and amount
        $totalExpensesCount = $group->expenses()->whereNull('deleted_at')->count();
        $totalExpensesAmount = round((float) $group->expenses()->whereNull('deleted_at')->sum('amount'), 2);

        return Inertia::render('Groups/Show', [
            'group' => $group,
            'isAdmin' => $group->isAdmin($request->user()),
            'recentExpenses' => $recentExpenses,
            'totalExpensesCount' => $totalExpensesCount,
            'totalExpensesAmount' => $totalExpensesAmount,
        ]);
    }

    public function edit(Request $request, Group $group): Response
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        return Inertia::render('Groups/Edit', [
            'group' => $group,
        ]);
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $group->update($validated);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group updated successfully.');
    }

    public function destroy(Request $request, Group $group): RedirectResponse
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Group deleted successfully.');
    }

    public function refreshInviteCode(Request $request, Group $group): RedirectResponse
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        $group->update([
            'invite_code' => Group::generateInviteCode(),
        ]);

        return back()->with('success', 'Invite code refreshed.');
    }

    public function join(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invite_code' => ['required', 'string', 'size:8'],
        ]);

        $group = Group::where('invite_code', strtoupper($validated['invite_code']))->first();

        if (!$group) {
            return back()->withErrors(['invite_code' => 'Invalid invite code. Please check and try again.']);
        }

        if ($group->isMember($request->user())) {
            return redirect()->route('groups.show', $group)
                ->with('info', 'You are already a member of this group.');
        }

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
            'role' => 'member',
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'You have joined the group.');
    }

    public function joinViaLink(Request $request, string $inviteCode): Response|RedirectResponse
    {
        $group = Group::where('invite_code', strtoupper($inviteCode))->first();

        if (!$group) {
            abort(404);
        }

        // If already a member, redirect to group page
        if ($request->user() && $group->isMember($request->user())) {
            return redirect()->route('groups.show', $group);
        }

        return Inertia::render('Groups/Join', [
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'members_count' => $group->groupMembers()->count(),
                'invite_code' => $group->invite_code,
            ],
        ]);
    }

    public function leave(Request $request, Group $group): RedirectResponse
    {
        return back()->withErrors(['group' => 'Members cannot leave a group. Only the admin can remove members.']);
    }

    public function removeMember(Request $request, Group $group, int $userId): RedirectResponse
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        if ($userId === $request->user()->id) {
            return back()->withErrors(['member' => 'You cannot remove yourself. Use "Leave Group" instead.']);
        }

        $membership = $group->groupMembers()
            ->where('user_id', $userId)
            ->first();

        if (!$membership) {
            return back()->withErrors(['member' => 'User is not a member of this group.']);
        }

        if ($membership->role === 'admin') {
            return back()->withErrors(['member' => 'Cannot remove a group admin.']);
        }

        // Check if group has any expenses
        if ($group->expenses()->exists()) {
            return back()->withErrors(['member' => 'Cannot remove member — group has expenses. Settle all expenses first.']);
        }

        $membership->delete();

        return back()->with('success', 'Member removed from group.');
    }

    public function searchUsers(Request $request, Group $group): JsonResponse
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        $phone = $request->input('phone', '');

        if (strlen($phone) < 3) {
            return response()->json([]);
        }

        $existingMemberIds = $group->groupMembers()->pluck('user_id');

        $users = User::where('phone', 'LIKE', $phone . '%')
            ->whereNotNull('phone_verified_at')
            ->whereNotIn('id', $existingMemberIds)
            ->select('id', 'name', 'phone', 'avatar')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function addMember(Request $request, Group $group): RedirectResponse
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        if (!$user->phone_verified_at) {
            return back()->withErrors(['member' => 'This user has not verified their phone number.']);
        }

        if ($group->isMember($user)) {
            return back()->withErrors(['member' => 'This user is already a member of the group.']);
        }

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role' => 'member',
        ]);

        // Notify the added user
        $user->notify(new AddedToGroup($group, $request->user()->name));

        return back()->with('success', $user->name . ' has been added to the group.');
    }
}
