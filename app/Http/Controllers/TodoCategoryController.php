<?php

namespace App\Http\Controllers;

use App\Models\TodoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoCategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        TodoCategory::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Category created.');
    }

    public function update(Request $request, TodoCategory $todoCategory)
    {
        $this->authorize($todoCategory);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        $todoCategory->update($validated);

        return redirect()->back()->with('success', 'Category updated.');
    }

    public function destroy(TodoCategory $todoCategory)
    {
        $this->authorize($todoCategory);

        $todoCategory->delete();

        return redirect()->back()->with('success', 'Category deleted.');
    }

    private function authorize(TodoCategory $category): void
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
