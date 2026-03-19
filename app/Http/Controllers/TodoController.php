<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Todo;
use App\Models\TodoCategory;
use App\Models\User;
use App\Notifications\TodoAssigned;
use App\Notifications\TodoReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Todo::with(['category', 'assignee:id,name,avatar', 'user:id,name,avatar'])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhere('assigned_to', $userId);
            });

        // Filter by ownership
        if ($request->filled('scope')) {
            if ($request->scope === 'mine') {
                $query->where('user_id', $userId)->where(function ($q) use ($userId) {
                    $q->whereNull('assigned_to')->orWhere('assigned_to', $userId);
                });
            } elseif ($request->scope === 'assigned_by_me') {
                $query->where('user_id', $userId)->whereNotNull('assigned_to')->where('assigned_to', '!=', $userId);
            } elseif ($request->scope === 'assigned_to_me') {
                $query->where('assigned_to', $userId)->where('user_id', '!=', $userId);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'completed') {
                $query->completed();
            }
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('todo_category_id', $request->category);
        }

        $todos = $query->orderByRaw("is_completed ASC, CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC, created_at DESC")
            ->paginate(30)
            ->withQueryString();

        // Stats include own + assigned-to-me todos
        $allQuery = fn () => Todo::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('assigned_to', $userId);
        });

        $stats = [
            'total' => $allQuery()->count(),
            'pending' => $allQuery()->pending()->count(),
            'completed' => $allQuery()->where('is_completed', true)->count(),
            'overdue' => $allQuery()->pending()
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->startOfDay())
                ->count(),
        ];

        $categories = TodoCategory::where('user_id', $userId)
            ->withCount('todos')
            ->orderBy('name')
            ->get();

        // Contacts for assignment dropdown
        $contacts = Contact::with('contactUser:id,name,avatar')
            ->where('user_id', $userId)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->contactUser->id,
                'name' => $c->contactUser->name,
                'avatar' => $c->contactUser->avatar,
            ]);

        return Inertia::render('Todos/Index', [
            'todos' => $todos,
            'stats' => $stats,
            'categories' => $categories,
            'contacts' => $contacts,
            'filters' => $request->only(['status', 'priority', 'category', 'scope']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'todo_category_id' => 'nullable|exists:todo_categories,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $todo = Todo::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        // Notify assignee
        if ($todo->assigned_to && $todo->assigned_to !== Auth::id()) {
            $assignee = User::find($todo->assigned_to);
            $assignee->notify(new TodoAssigned($todo, Auth::user()->name));
        }

        return redirect()->back()->with('success', 'Task added successfully.');
    }

    public function update(Request $request, Todo $todo)
    {
        $this->authorizeTodo($todo);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'todo_category_id' => 'nullable|exists:todo_categories,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // Reset reminder_sent if reminder time changed
        if (isset($validated['reminder_at']) && $validated['reminder_at'] !== $todo->reminder_at?->toDateTimeString()) {
            $validated['reminder_sent'] = false;
        }

        $oldAssignee = $todo->assigned_to;
        $todo->update($validated);

        // Notify new assignee if changed
        if ($todo->assigned_to && $todo->assigned_to !== Auth::id() && $todo->assigned_to !== $oldAssignee) {
            $assignee = User::find($todo->assigned_to);
            $assignee->notify(new TodoAssigned($todo, Auth::user()->name));
        }

        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    public function toggle(Todo $todo)
    {
        $this->authorizeTodoOrAssignee($todo);

        $todo->update([
            'is_completed' => !$todo->is_completed,
            'completed_at' => !$todo->is_completed ? now() : null,
        ]);

        return redirect()->back();
    }

    public function destroy(Todo $todo)
    {
        $this->authorizeTodo($todo);

        $todo->delete();

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }

    /**
     * Lightweight endpoint for frontend alarm polling.
     */
    public function checkReminders()
    {
        $user = Auth::user();

        $reminders = $user->unreadNotifications()
            ->where('type', TodoReminder::class)
            ->limit(10)
            ->get();

        if ($reminders->isEmpty()) {
            return response()->json([]);
        }

        $data = $reminders->map(fn ($n) => [
            'id' => $n->id,
            'title' => $n->data['title'] ?? '',
            'priority' => $n->data['priority'] ?? 'medium',
            'due_date' => $n->data['due_date'] ?? null,
        ]);

        $reminders->markAsRead();

        return response()->json($data);
    }

    private function authorizeTodo(Todo $todo): void
    {
        if ($todo->user_id !== Auth::id()) {
            abort(403);
        }
    }

    private function authorizeTodoOrAssignee(Todo $todo): void
    {
        if ($todo->user_id !== Auth::id() && $todo->assigned_to !== Auth::id()) {
            abort(403);
        }
    }
}
