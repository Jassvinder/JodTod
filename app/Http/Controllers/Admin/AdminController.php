<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with overview stats.
     */
    public function dashboard(): Response
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        // Overall counts
        $totalUsers = User::count();
        $totalGroups = Group::count();
        $totalExpenses = Expense::count();
        $totalAmountTracked = round((float) Expense::sum('amount'), 2);

        // This month stats
        $newUsersThisMonth = User::where('created_at', '>=', $startOfMonth)->count();
        $newGroupsThisMonth = Group::where('created_at', '>=', $startOfMonth)->count();

        // Recent 5 users
        $recentUsers = User::orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'name', 'email', 'avatar', 'role', 'created_at']);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => $totalUsers,
                'total_groups' => $totalGroups,
                'total_expenses' => $totalExpenses,
                'total_amount_tracked' => $totalAmountTracked,
                'new_users_this_month' => $newUsersThisMonth,
                'new_groups_this_month' => $newGroupsThisMonth,
            ],
            'recentUsers' => $recentUsers,
        ]);
    }

    /**
     * Display paginated user management list.
     */
    public function users(Request $request): Response
    {
        $query = User::query();

        // Search filter (name or email)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($role = $request->input('role')) {
            if (in_array($role, ['admin', 'user'])) {
                $query->where('role', $role);
            }
        }

        $users = $query->withCount(['expenses', 'groups'])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString()
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'email_verified_at' => $user->email_verified_at,
                    'phone_verified_at' => $user->phone_verified_at,
                    'avatar' => $user->avatar,
                    'created_at' => $user->created_at,
                    'expenses_count' => $user->expenses_count,
                    'groups_count' => $user->groups_count,
                ];
            });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $request->input('search', ''),
                'role' => $request->input('role', ''),
            ],
        ]);
    }

    /**
     * Update a user's role.
     */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'in:admin,user'],
        ]);

        // Cannot change own role
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', "User role updated to {$request->role}.");
    }

    /**
     * Delete a user account.
     */
    public function deleteUser(Request $request, User $user): RedirectResponse
    {
        // Cannot delete self
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "User \"{$userName}\" has been deleted.");
    }

    /**
     * Display paginated group management list.
     */
    public function groups(Request $request): Response
    {
        $query = Group::withCount(['members', 'expenses'])
            ->with(['creator:id,name,email']);

        // Search filter (group name)
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $groups = $query->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Groups/Index', [
            'groups' => $groups,
            'filters' => [
                'search' => $request->input('search', ''),
            ],
        ]);
    }

    /**
     * Display detailed view of a single group.
     */
    public function groupDetail(Group $group): Response
    {
        $group->load([
            'members.user:id,name,email,avatar,phone',
            'expenses.user',
            'expenses.splits.user',
            'settlements',
        ]);

        $totalExpenses = round((float) $group->expenses->sum('amount'), 2);

        return Inertia::render('Admin/Groups/Show', [
            'group' => $group,
            'totalExpenses' => $totalExpenses,
        ]);
    }

    /**
     * Display detailed view of a single user.
     */
    public function userDetail(User $user): Response
    {
        $user->loadCount(['expenses', 'groups']);

        $recentExpenses = Expense::where('user_id', $user->id)
            ->with('category')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $groups = $user->groups()
            ->withCount('members')
            ->get();

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'recentExpenses' => $recentExpenses,
            'groups' => $groups,
        ]);
    }
}
