<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Notifications\AddedToGroup;
use App\Notifications\GroupJoinRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    /**
     * List user's groups with member count and pivot role.
     */
    public function index(Request $request): JsonResponse
    {
        $groups = $request->user()
            ->groups()
            ->withCount('members')
            ->withPivot('role')
            ->latest()
            ->get();

        return $this->success($groups);
    }

    /**
     * Create a new group. Creator becomes admin.
     */
    public function store(Request $request): JsonResponse
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

        $group->load(['members' => function ($query) {
            $query->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.avatar');
        }]);

        return $this->created($group, 'Group created successfully.');
    }

    /**
     * Group detail with members, recent expenses, stats, contacts (admin only).
     */
    public function show(Request $request, Group $group): JsonResponse
    {
        if (!$group->isMember($request->user())) {
            return $this->forbidden('You are not a member of this group.');
        }

        $group->load(['members' => function ($query) {
            $query->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.avatar')
                ->orderByPivot('joined_at');
        }, 'creator:id,name']);

        // Recent 5 expenses
        $recentExpenses = $group->expenses()
            ->with(['category', 'payer'])
            ->whereNull('deleted_at')
            ->orderBy('expense_date', 'desc')
            ->limit(5)
            ->get();

        // Total expenses count and amount
        $totalExpensesCount = $group->expenses()->whereNull('deleted_at')->count();
        $totalExpensesAmount = round((float) $group->expenses()->whereNull('deleted_at')->sum('amount'), 2);

        // Members with unsettled expenses (for remove vs deactivate logic)
        $unsettledPayerIds = $group->expenses()
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->pluck('paid_by')
            ->unique()
            ->toArray();

        $unsettledSplitUserIds = DB::table('expense_splits')
            ->join('expenses', 'expenses.id', '=', 'expense_splits.expense_id')
            ->where('expenses.group_id', $group->id)
            ->where('expenses.is_settled', false)
            ->whereNull('expenses.deleted_at')
            ->pluck('expense_splits.user_id')
            ->unique()
            ->toArray();

        $membersWithUnsettled = array_values(array_unique(array_merge($unsettledPayerIds, $unsettledSplitUserIds)));

        // Get admin's contacts (excluding existing group members) for add member
        $contacts = [];
        if ($group->isAdmin($request->user())) {
            $memberIds = $group->members->pluck('id')->toArray();
            $contacts = Contact::with('contactUser:id,name,email,phone,avatar')
                ->where('user_id', $request->user()->id)
                ->whereNotIn('contact_user_id', $memberIds)
                ->get()
                ->map(fn ($c) => $c->contactUser);
        }

        // Pending members (for admin approval)
        $pendingMembers = [];
        if ($group->isAdmin($request->user())) {
            $pendingMembers = $group->pendingMembers()
                ->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.avatar')
                ->get();
        }

        return $this->success([
            'group' => $group,
            'isAdmin' => $group->isAdmin($request->user()),
            'recentExpenses' => $recentExpenses,
            'totalExpensesCount' => $totalExpensesCount,
            'totalExpensesAmount' => $totalExpensesAmount,
            'contacts' => $contacts,
            'membersWithUnsettled' => $membersWithUnsettled,
            'pendingMembers' => $pendingMembers,
        ]);
    }

    /**
     * Update group name/description. Admin only.
     */
    public function update(Request $request, Group $group): JsonResponse
    {
        if (!$group->isAdmin($request->user())) {
            return $this->forbidden('Only group admin can update the group.');
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
            // Delete old photo
            if ($group->photo) {
                Storage::disk('public')->delete($group->photo);
            }
            $data['photo'] = $request->file('photo')->store('groups', 'public');
        } elseif ($request->boolean('remove_photo') && $group->photo) {
            Storage::disk('public')->delete($group->photo);
            $data['photo'] = null;
        }

        $group->update($data);

        return $this->success($group, 'Group updated successfully.');
    }

    /**
     * Delete group. Admin only. Block if unsettled expenses exist.
     */
    public function destroy(Request $request, Group $group): JsonResponse
    {
        if (!$group->isAdmin($request->user())) {
            return $this->forbidden('Only group admin can delete the group.');
        }

        $hasUnsettled = $group->expenses()
            ->where('is_settled', false)
            ->whereNull('deleted_at')
            ->exists();

        if ($hasUnsettled) {
            return $this->error('Cannot delete group — there are unsettled expenses. Settle all expenses first.', 422);
        }

        $group->delete();

        return $this->success(null, 'Group deleted successfully.');
    }

    /**
     * Join a group via 8-char invite code. Creates a pending request for admin approval.
     */
    public function join(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invite_code' => ['required', 'string', 'size:8'],
        ]);

        $group = Group::where('invite_code', strtoupper($validated['invite_code']))->first();

        if (!$group) {
            return $this->error('Invalid invite code. Please check and try again.', 404);
        }

        if ($group->isMember($request->user())) {
            return $this->success($group, 'You are already a member of this group.');
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

        return $this->success($group, 'Join request sent. The group admin will review your request.');
    }

    /**
     * Approve a pending member. Admin only.
     */
    public function approveMember(Request $request, Group $group, int $userId): JsonResponse
    {
        if (!$group->isAdmin($request->user())) {
            return $this->forbidden('Only group admin can approve members.');
        }

        $membership = $group->groupMembers()
            ->where('user_id', $userId)
            ->where('is_approved', false)
            ->first();

        if (!$membership) {
            return $this->error('No pending request found for this user.', 404);
        }

        $membership->update(['is_approved' => true]);

        // Notify the approved user
        $user = User::find($userId);
        if ($user) {
            $user->notify(new AddedToGroup($group, $request->user()->name));
        }

        return $this->success(null, 'Member approved successfully.');
    }

    /**
     * Reject a pending member. Admin only.
     */
    public function rejectMember(Request $request, Group $group, int $userId): JsonResponse
    {
        if (!$group->isAdmin($request->user())) {
            return $this->forbidden('Only group admin can reject members.');
        }

        $membership = $group->groupMembers()
            ->where('user_id', $userId)
            ->where('is_approved', false)
            ->first();

        if (!$membership) {
            return $this->error('No pending request found for this user.', 404);
        }

        $membership->delete();

        return $this->success(null, 'Join request rejected.');
    }

    /**
     * Leave a group. Admin cannot leave. If unsettled expenses, deactivate instead.
     */
    public function leave(Request $request, Group $group): JsonResponse
    {
        $user = $request->user();

        if (!$group->isMember($user)) {
            return $this->error('You are not a member of this group.', 422);
        }

        if ($group->isAdmin($user)) {
            return $this->error('Group admin cannot leave. Transfer admin role or delete the group.', 422);
        }

        // Check if member is involved in any unsettled expense
        $unsettledAsPayer = $group->expenses()
            ->where('paid_by', $user->id)
            ->where('is_settled', false)
            ->exists();

        $unsettledInSplits = DB::table('expense_splits')
            ->join('expenses', 'expenses.id', '=', 'expense_splits.expense_id')
            ->where('expenses.group_id', $group->id)
            ->where('expenses.is_settled', false)
            ->where('expense_splits.user_id', $user->id)
            ->exists();

        if ($unsettledAsPayer || $unsettledInSplits) {
            $group->groupMembers()->where('user_id', $user->id)->update(['is_active' => false]);

            return $this->success(null, "You have been deactivated from \"{$group->name}\". You won't be included in new expenses but your pending balances remain for settlement.");
        }

        $group->groupMembers()->where('user_id', $user->id)->delete();

        return $this->success(null, "You have left \"{$group->name}\".");
    }

    /**
     * Add a member from contacts. Admin only.
     */
    public function addMember(Request $request, Group $group): JsonResponse
    {
        if (!$group->isAdmin($request->user())) {
            return $this->forbidden('Only group admin can add members.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        if (!$user->phone_verified_at) {
            return $this->error('This user has not verified their phone number.', 422);
        }

        if ($group->isMember($user)) {
            return $this->error('This user is already a member of the group.', 422);
        }

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role' => 'member',
        ]);

        // Notify the added user
        $user->notify(new AddedToGroup($group, $request->user()->name));

        return $this->success(null, $user->name . ' has been added to the group.');
    }

    /**
     * Remove or deactivate a member. Admin only.
     * If unsettled expenses exist, deactivate (is_active=false) instead of delete.
     */
    public function removeMember(Request $request, Group $group, int $userId): JsonResponse
    {
        if (!$group->isAdmin($request->user())) {
            return $this->forbidden('Only group admin can remove members.');
        }

        if ($userId === $request->user()->id) {
            return $this->error('You cannot remove yourself. Use "Leave Group" instead.', 422);
        }

        $membership = $group->groupMembers()
            ->where('user_id', $userId)
            ->first();

        if (!$membership) {
            return $this->error('User is not a member of this group.', 404);
        }

        if ($membership->role === 'admin') {
            return $this->error('Cannot remove a group admin.', 422);
        }

        // Check if member is involved in any unsettled expense
        $unsettledAsPayer = $group->expenses()
            ->where('paid_by', $userId)
            ->where('is_settled', false)
            ->exists();

        $unsettledInSplits = DB::table('expense_splits')
            ->join('expenses', 'expenses.id', '=', 'expense_splits.expense_id')
            ->where('expenses.group_id', $group->id)
            ->where('expenses.is_settled', false)
            ->where('expense_splits.user_id', $userId)
            ->exists();

        if ($unsettledAsPayer || $unsettledInSplits) {
            $membership->update(['is_active' => false]);

            return $this->success(null, 'Member has unsettled expenses and has been deactivated. They won\'t be included in new expenses but their pending balances remain for settlement.');
        }

        $membership->delete();

        return $this->success(null, 'Member removed from group.');
    }

    /**
     * Reactivate a deactivated member. Admin only.
     */
    public function reactivateMember(Request $request, Group $group, int $userId): JsonResponse
    {
        if (!$group->isAdmin($request->user())) {
            return $this->forbidden('Only group admin can reactivate members.');
        }

        $membership = $group->groupMembers()
            ->where('user_id', $userId)
            ->first();

        if (!$membership) {
            return $this->error('User is not a member of this group.', 404);
        }

        $membership->update(['is_active' => true]);

        return $this->success(null, 'Member has been reactivated.');
    }
}
