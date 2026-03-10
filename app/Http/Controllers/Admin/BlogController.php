<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogController extends Controller
{
    /**
     * List all blog posts for admin management.
     */
    public function index()
    {
        $posts = BlogPost::with('author:id,name')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/Blog/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show blog post creation form.
     */
    public function create()
    {
        return Inertia::render('Admin/Blog/Create');
    }

    /**
     * Store a new blog post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'featured_image' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        $validated['author_id'] = $request->user()->id;
        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);

        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (BlogPost::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Auto-set published_at when publishing
        if (!empty($validated['is_published']) && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        BlogPost::create($validated);

        return redirect()->route('admin.blog')
            ->with('success', 'Blog post created successfully.');
    }

    /**
     * Show blog post edit form.
     */
    public function edit(BlogPost $post)
    {
        return Inertia::render('Admin/Blog/Edit', [
            'post' => $post,
        ]);
    }

    /**
     * Update an existing blog post.
     */
    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'featured_image' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        // If toggling from unpublished to published, set published_at
        if (!empty($validated['is_published']) && !$post->is_published) {
            $validated['published_at'] = now();
        }

        // If toggling from published to unpublished, clear published_at
        if (empty($validated['is_published']) && $post->is_published) {
            $validated['published_at'] = null;
        }

        $post->update($validated);

        return redirect()->route('admin.blog')
            ->with('success', 'Blog post updated successfully.');
    }

    /**
     * Delete a blog post.
     */
    public function destroy(BlogPost $post)
    {
        $post->delete();

        return redirect()->route('admin.blog')
            ->with('success', 'Blog post deleted successfully.');
    }
}
