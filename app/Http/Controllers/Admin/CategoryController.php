<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display all categories with expense counts.
     */
    public function index(): Response
    {
        $categories = Category::withCount('expenses')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new category.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:categories,name'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        Category::create([
            'name' => $request->name,
            'icon' => $request->icon,
        ]);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:categories,name,' . $category->id],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $category->update([
            'name' => $request->name,
            'icon' => $request->icon,
        ]);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    /**
     * Delete a category (only if no expenses use it).
     */
    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if ($category->expenses()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete category that has expenses. Reassign expenses first.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
