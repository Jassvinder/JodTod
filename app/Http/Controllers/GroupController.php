<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Notifications\AddedToGroup;
use App\Notifications\GroupJoinRequest;
use App\Notifications\JoinRequestRejected;
use App\Notifications\RemovedFromGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        // Add settlement status for each group
        $groups->each(function ($group) {
            $hasExpenses = $group->expenses()->whereNull('deleted_at')->exists();
            $hasUnsettled = $group->expenses()->where('is_settled', false)->whereNull('deleted_at')->exists();
            $group->is_all_settled = $hasExpenses && !$hasUnsettled;
        });

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
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by' => $request->user()->id,
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('groups', 'public');
        }

        $group = Group::create($data);

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

        // Check which members have unsettled expenses (for remove vs deactivate label)
        $unsettledPayerIds = $group->expenses()
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->pluck('paid_by')
            ->unique()
            ->toArray();

        $unsettledSplitUserIds = \DB::table('expense_splits')
            ->join('expenses', 'expenses.id', '=', 'expense_splits.expense_id')
            ->where('expenses.group_id', $group->id)
            ->where('expenses.is_settled', false)
            ->whereNull('expenses.deleted_at')
            ->pluck('expense_splits.user_id')
            ->unique()
            ->toArray();

        $membersWithUnsettled = array_values(array_unique(array_merge($unsettledPayerIds, $unsettledSplitUserIds)));

        // Get admin's contacts (excluding existing group members) for add member
        $memberIds = $group->members->pluck('id')->toArray();
        $contacts = \App\Models\Contact::with('contactUser:id,name,email,phone,avatar')
            ->where('user_id', $request->user()->id)
            ->whereNotIn('contact_user_id', $memberIds)
            ->get()
            ->map(fn ($c) => $c->contactUser);

        // Pending members (for admin approval)
        $pendingMembers = [];
        if ($group->isAdmin($request->user())) {
            $pendingMembers = $group->pendingMembers()
                ->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.avatar')
                ->get();
        }

        // Calculate each member's total share across ALL expenses (settled + unsettled)
        $allExpenseIds = $group->expenses()->whereNull('deleted_at')->pluck('id');
        $sharesPerUser = \DB::table('expense_splits')
            ->whereIn('expense_id', $allExpenseIds)
            ->groupBy('user_id')
            ->selectRaw('user_id, SUM(share_amount) as total_share')
            ->pluck('total_share', 'user_id');

        $memberShares = $group->members->map(fn ($m) => [
            'user_id' => $m->id,
            'name' => $m->name,
            'avatar' => $m->avatar,
            'total_share' => round((float) ($sharesPerUser[$m->id] ?? 0), 2),
        ])->values();

        return Inertia::render('Groups/Show', [
            'group' => $group,
            'isAdmin' => $group->isAdmin($request->user()),
            'recentExpenses' => $recentExpenses,
            'totalExpensesCount' => $totalExpensesCount,
            'totalExpensesAmount' => $totalExpensesAmount,
            'contacts' => $contacts,
            'membersWithUnsettled' => $membersWithUnsettled,
            'pendingMembers' => $pendingMembers,
            'memberShares' => $memberShares,
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
            'photo' => ['nullable', 'image', 'max:5120'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('photo')) {
            if ($group->photo) {
                Storage::disk('public')->delete($group->photo);
            }
            $data['photo'] = $request->file('photo')->store('groups', 'public');
        } elseif ($request->boolean('remove_photo') && $group->photo) {
            Storage::disk('public')->delete($group->photo);
            $data['photo'] = null;
        }

        $group->update($data);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group updated successfully.');
    }

    public function destroy(Request $request, Group $group): RedirectResponse
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        $hasUnsettled = $group->expenses()
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->exists();

        if ($hasUnsettled) {
            return back()->withErrors(['group' => 'Cannot delete group — there are unsettled expenses. Settle all expenses first.']);
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
            'is_approved' => false,
        ]);

        // Notify group admins about the join request
        $admins = $group->groupMembers()->where('role', 'admin')->with('user')->get();
        foreach ($admins as $adminMembership) {
            $adminMembership->user->notify(new GroupJoinRequest($group, $request->user()));
        }

        return redirect()->route('groups.index')
            ->with('success', 'Join request sent. The group admin will review your request.');
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
        $user = $request->user();

        if (!$group->isMember($user)) {
            return back()->withErrors(['group' => 'You are not a member of this group.']);
        }

        if ($group->isAdmin($user)) {
            return back()->withErrors(['group' => 'Group admin cannot leave. Transfer admin role or delete the group.']);
        }

        // Check if member is involved in any unsettled expense (as payer or in splits)
        $unsettledAsPayer = $group->expenses()
            ->where('paid_by', $user->id)
            ->where('is_settled', false)
            ->exists();

        $unsettledInSplits = \DB::table('expense_splits')
            ->join('expenses', 'expenses.id', '=', 'expense_splits.expense_id')
            ->where('expenses.group_id', $group->id)
            ->where('expenses.is_settled', false)
            ->where('expense_splits.user_id', $user->id)
            ->exists();

        if ($unsettledAsPayer || $unsettledInSplits) {
            // Block member instead of preventing leave - they won't be in new expense splits
            $group->groupMembers()->where('user_id', $user->id)->update(['is_active' => false]);
            return redirect()->route('groups.index')->with('success', "You have been deactivated from \"{$group->name}\". You won't be included in new expenses but your pending balances remain for settlement.");
        }

        $group->groupMembers()->where('user_id', $user->id)->delete();

        return redirect()->route('groups.index')->with('success', "You have left \"{$group->name}\".");
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

        // Check if member is involved in any unsettled expense (as payer or in splits)
        $unsettledAsPayer = $group->expenses()
            ->where('paid_by', $userId)
            ->where('is_settled', false)
            ->exists();

        $unsettledInSplits = \DB::table('expense_splits')
            ->join('expenses', 'expenses.id', '=', 'expense_splits.expense_id')
            ->where('expenses.group_id', $group->id)
            ->where('expenses.is_settled', false)
            ->where('expense_splits.user_id', $userId)
            ->exists();

        $removedUser = User::find($userId);

        if ($unsettledAsPayer || $unsettledInSplits) {
            $membership->update(['is_active' => false]);

            if ($removedUser) {
                $removedUser->notify(new RemovedFromGroup($group, $request->user()->name, true));
            }

            return back()->with('success', 'Member has unsettled expenses and has been deactivated. They won\'t be included in new expenses but their pending balances remain for settlement.');
        }

        $membership->delete();

        if ($removedUser) {
            $removedUser->notify(new RemovedFromGroup($group, $request->user()->name));
        }

        return back()->with('success', 'Member removed from group.');
    }

    public function reactivateMember(Request $request, Group $group, int $userId): RedirectResponse
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        $membership = $group->groupMembers()
            ->where('user_id', $userId)
            ->first();

        if (!$membership) {
            return back()->withErrors(['member' => 'User is not a member of this group.']);
        }

        $membership->update(['is_active' => true]);

        return back()->with('success', 'Member has been reactivated.');
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

    public function approveMember(Request $request, Group $group, int $userId): RedirectResponse
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        $membership = $group->groupMembers()
            ->where('user_id', $userId)
            ->where('is_approved', false)
            ->first();

        if (!$membership) {
            return back()->withErrors(['member' => 'No pending request found for this user.']);
        }

        $membership->update(['is_approved' => true]);

        $user = User::find($userId);
        if ($user) {
            $user->notify(new AddedToGroup($group, $request->user()->name));
        }

        return back()->with('success', 'Member approved successfully.');
    }

    public function rejectMember(Request $request, Group $group, int $userId): RedirectResponse
    {
        if (!$group->isAdmin($request->user())) {
            abort(403);
        }

        $membership = $group->groupMembers()
            ->where('user_id', $userId)
            ->where('is_approved', false)
            ->first();

        if (!$membership) {
            return back()->withErrors(['member' => 'No pending request found for this user.']);
        }

        $rejectedUser = User::find($userId);
        $membership->delete();

        if ($rejectedUser) {
            $rejectedUser->notify(new JoinRequestRejected($group));
        }

        return back()->with('success', 'Join request rejected.');
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
