@extends('layouts.public')

@section('title', 'Blog - JodTod | Tips on Expense Management & Splitting')
@section('meta_description', 'Read the JodTod blog for tips on expense tracking, group expense splitting, budgeting, and personal finance management.')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-3xl sm:text-4xl font-bold">JodTod Blog</h1>
            <p class="mt-4 text-lg text-primary-100">Tips, guides, and insights on expense management, splitting, and personal finance.</p>
        </div>
    </div>
</section>

<!-- Blog Posts Grid -->
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(isset($posts) && $posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                <article class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Featured Image -->
                    <div class="aspect-video bg-gray-100 overflow-hidden">
                        @if($post->featured_image)
                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
                                <svg class="w-12 h-12 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <div class="flex items-center gap-3 text-sm text-gray-500">
                            <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                            @if($post->author)
                                <span class="text-gray-300">|</span>
                                <span>{{ $post->author->name ?? 'JodTod Team' }}</span>
                            @endif
                        </div>

                        <h2 class="mt-3 text-xl font-bold text-gray-900">
                            <a href="{{ url('/blog/' . $post->slug) }}" class="hover:text-primary-600 transition-colors">
                                {{ $post->title }}
                            </a>
                        </h2>

                        <p class="mt-2 text-gray-600 line-clamp-3">
                            {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 150) }}
                        </p>

                        <a href="{{ url('/blog/' . $post->slug) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                            Read More
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($posts->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $posts->links() }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-900">No posts yet</h2>
                <p class="mt-2 text-gray-600">Check back soon! We're working on some great content.</p>
                <a href="{{ url('/') }}" class="mt-6 inline-flex items-center justify-center px-6 py-3 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors">
                    Back to Home
                </a>
            </div>
        @endif

    </div>
</section>
@endsection
