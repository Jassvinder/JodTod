<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    /**
     * List published blog posts for public viewing.
     */
    public function index()
    {
        $posts = BlogPost::published()
            ->with('author:id,name')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('pages.public.blog.index', compact('posts'));
    }

    /**
     * Show a single published blog post by slug.
     */
    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->with('author:id,name')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.public.blog.show', compact('post'));
    }
}
