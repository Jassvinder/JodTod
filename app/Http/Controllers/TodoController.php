<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $query = Todo::forUser(Auth::id());

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

        $todos = $query->orderByRaw("is_completed ASC, CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC, created_at DESC")
            ->paginate(30)
            ->withQueryString();

        $stats = [
            'total' => Todo::forUser(Auth::id())->count(),
            'pending' => Todo::forUser(Auth::id())->pending()->count(),
            'completed' => Todo::forUser(Auth::id())->completed()->count(),
            'overdue' => Todo::forUser(Auth::id())->pending()
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->startOfDay())
                ->count(),
        ];

        return Inertia::render('Todos/Index', [
            'todos' => $todos,
            'stats' => $stats,
            'filters' => $request->only(['status', 'priority']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        Todo::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Task added successfully.');
    }

    public function update(Request $request, Todo $todo)
    {
        $this->authorizeTodo($todo);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $todo->update($validated);

        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    public function toggle(Todo $todo)
    {
        $this->authorizeTodo($todo);

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

    private function authorizeTodo(Todo $todo): void
    {
        if ($todo->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
