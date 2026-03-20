<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\User;
use App\Notifications\TodoAssigned;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $query = Todo::with(['category', 'assignee:id,name,avatar', 'user:id,name,avatar'])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhere('assigned_to', $userId);
            });

        // Filter by scope
        if ($request->filled('scope')) {
            if ($request->scope === 'assigned_to_me') {
                $query->where('assigned_to', $userId)->where('user_id', '!=', $userId);
            } elseif ($request->scope === 'assigned_by_me') {
                $query->where('user_id', $userId)->whereNotNull('assigned_to')->where('assigned_to', '!=', $userId);
            }
            // 'all' scope = default, no extra filter needed
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
        if ($request->filled('category_id')) {
            $query->where('todo_category_id', $request->category_id);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $paginator = $query
            ->orderByRaw("is_completed ASC, CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC, created_at DESC")
            ->paginate(20);

        // Stats
        $allQuery = fn () => Todo::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('assigned_to', $userId);
        });

        $stats = [
            'total' => $allQuery()->count(),
            'pending' => $allQuery()->pending()->count(),
            'completed' => $allQuery()->completed()->count(),
            'overdue' => $allQuery()->pending()
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->startOfDay())
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'stats' => $stats,
        ], 200);
    }

    public function store(Request $request): JsonResponse
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

        $todo->load(['category', 'assignee:id,name,avatar']);

        return $this->created($todo, 'Task added successfully.');
    }

    public function update(Request $request, Todo $todo): JsonResponse
    {
        $this->authorizeTodoOrAssignee($todo);

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

        $todo->load(['category', 'assignee:id,name,avatar']);

        return $this->success($todo, 'Task updated successfully.');
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $this->authorizeTodoOwner($todo);

        $todo->delete();

        return $this->success(null, 'Task deleted successfully.');
    }

    public function toggle(Todo $todo): JsonResponse
    {
        $this->authorizeTodoOrAssignee($todo);

        $todo->update([
            'is_completed' => !$todo->is_completed,
            'completed_at' => !$todo->is_completed ? now() : null,
        ]);

        $todo->load(['category', 'assignee:id,name,avatar']);

        return $this->success($todo, $todo->is_completed ? 'Task completed.' : 'Task marked as pending.');
    }

    private function authorizeTodoOwner(Todo $todo): void
    {
        if ($todo->user_id !== Auth::id()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403));
        }
    }

    private function authorizeTodoOrAssignee(Todo $todo): void
    {
        if ($todo->user_id !== Auth::id() && $todo->assigned_to !== Auth::id()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403));
        }
    }
}
