<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\TodoCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        // Get user's own categories
        $ownCategoryIds = TodoCategory::where('user_id', Auth::id())->pluck('id');

        // Get category IDs from tasks assigned to the user (by others)
        $assignedCategoryIds = Todo::where('assigned_to', Auth::id())
            ->where('user_id', '!=', Auth::id())
            ->whereNotNull('category_id')
            ->pluck('category_id')
            ->unique();

        $allCategoryIds = $ownCategoryIds->merge($assignedCategoryIds)->unique();

        $categories = TodoCategory::whereIn('id', $allCategoryIds)
            ->withCount('todos')
            ->orderBy('name')
            ->get();

        return $this->success($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        $category = TodoCategory::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return $this->created($category, 'Category created successfully.');
    }

    public function update(Request $request, TodoCategory $todoCategory): JsonResponse
    {
        $this->authorizeCategory($todoCategory);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        $todoCategory->update($validated);

        return $this->success($todoCategory, 'Category updated successfully.');
    }

    public function destroy(TodoCategory $todoCategory): JsonResponse
    {
        $this->authorizeCategory($todoCategory);

        $todoCategory->delete();

        return $this->success(null, 'Category deleted successfully.');
    }

    private function authorizeCategory(TodoCategory $category): void
    {
        if ($category->user_id !== Auth::id()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403));
        }
    }
}
