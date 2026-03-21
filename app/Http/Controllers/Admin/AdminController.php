<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Setting;
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
            ->get(['id', 'name', 'email', 'phone', 'avatar', 'role', 'created_at']);

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
                    'banned_at' => $user->banned_at,
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
    public function editUser(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'in:admin,user'],
        ]);

        // Cannot change own role
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $user->forceFill(['role' => $request->role])->save();

        return redirect()->back()->with('success', "User role updated to {$request->role}.");
    }

    /**
     * Delete a user account.
     */
    public function deleteUser(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "User \"{$userName}\" has been deleted.");
    }

    public function banUser(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot ban yourself.');
        }

        $user->forceFill(['banned_at' => now()])->save();

        return redirect()->back()->with('success', "{$user->name} has been banned.");
    }

    public function unbanUser(Request $request, User $user): RedirectResponse
    {
        $user->forceFill(['banned_at' => null])->save();

        return redirect()->back()->with('success', "{$user->name} has been unbanned.");
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
            'members',
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

        $contacts = $user->contacts()
            ->with('contactUser:id,name,email,phone,avatar')
            ->get();

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'recentExpenses' => $recentExpenses,
            'groups' => $groups,
            'contacts' => $contacts,
        ]);
    }

    /**
     * Delete a user's contact (admin action).
     */
    public function deleteUserContact(User $user, Contact $contact): RedirectResponse
    {
        // Ensure the contact belongs to this user
        if ($contact->user_id !== $user->id) {
            abort(404);
        }

        $contact->delete();

        return redirect()->back()->with('success', 'Contact removed successfully.');
    }

    public function reports(): Response
    {
        $now = Carbon::now();

        // Monthly expense trends (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthlyTrends[] = [
                'month' => $month->format('M Y'),
                'expenses' => round((float) Expense::whereYear('expense_date', $month->year)
                    ->whereMonth('expense_date', $month->month)
                    ->sum('amount'), 2),
                'users' => User::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        // Category breakdown (top 10)
        $categoryBreakdown = \DB::table('expenses')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->whereNull('expenses.group_id')
            ->select('categories.name', \DB::raw('SUM(expenses.amount) as total'), \DB::raw('COUNT(*) as count'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Top groups by expense amount
        $topGroups = Group::withCount('members')
            ->withSum('expenses', 'amount')
            ->orderByDesc('expenses_sum_amount')
            ->limit(10)
            ->get()
            ->map(fn ($g) => [
                'name' => $g->name,
                'members' => $g->members_count,
                'total' => round((float) $g->expenses_sum_amount, 2),
            ]);

        // Settlement stats
        $totalSettlements = \DB::table('settlements')->count();
        $completedSettlements = \DB::table('settlements')->where('status', 'completed')->count();
        $pendingSettlements = \DB::table('settlements')->where('status', 'pending')->count();
        $totalSettledAmount = round((float) \DB::table('settlements')->where('status', 'completed')->sum('amount'), 2);

        return Inertia::render('Admin/Reports', [
            'monthlyTrends' => $monthlyTrends,
            'categoryBreakdown' => $categoryBreakdown,
            'topGroups' => $topGroups,
            'settlementStats' => [
                'total' => $totalSettlements,
                'completed' => $completedSettlements,
                'pending' => $pendingSettlements,
                'total_amount' => $totalSettledAmount,
            ],
        ]);
    }

    public function settings(): Response
    {
        return Inertia::render('Admin/Settings', [
            'settings' => [
                'site_name' => Setting::get('site_name', config('site.app.name')),
                'default_currency' => Setting::get('default_currency', 'INR'),
                'maintenance_mode' => Setting::get('maintenance_mode', '0'),
            ],
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:100',
            'default_currency' => 'required|string|max:5',
            'maintenance_mode' => 'required|in:0,1',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
