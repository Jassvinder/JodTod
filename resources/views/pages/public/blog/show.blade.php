@extends('layouts.public')

@section('title', ($post->meta_title ?? $post->title) . ' - JodTod Blog')
@section('meta_description', $post->meta_description ?? Str::limit(strip_tags($post->content), 160))

@section('meta_tags')
<!-- Article Meta Tags -->
<meta property="og:type" content="article">
<meta property="article:published_time" content="{{ $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String() }}">
@if($post->author)
<meta property="article:author" content="{{ $post->author->name }}">
@endif

<!-- Schema.org BlogPosting -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BlogPosting",
    "headline": "{{ $post->meta_title ?? $post->title }}",
    "description": "{{ $post->meta_description ?? Str::limit(strip_tags($post->content), 160) }}",
    @if($post->featured_image)
    "image": "{{ $post->featured_image }}",
    @endif
    "datePublished": "{{ $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String() }}",
    "dateModified": "{{ $post->updated_at->toIso8601String() }}",
    "author": {
        "@@type": "Person",
        "name": "{{ $post->author->name ?? 'JodTod Team' }}"
    },
    "publisher": {
        "@@type": "Organization",
        "name": "JodTod",
        "url": "{{ url('/') }}"
    },
    "mainEntityOfPage": {
        "@@type": "WebPage",
        "@@id": "{{ url()->current() }}"
    }
}
</script>
@endsection

@section('content')
<!-- Article Header -->
<section class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <!-- Back Link -->
        <a href="{{ url('/blog') }}" class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Blog
        </a>

        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 leading-tight">{{ $post->title }}</h1>

        <div class="mt-4 flex items-center gap-4 text-sm text-gray-500">
            @if($post->author)
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-medium">
                        {{ strtoupper(substr($post->author->name ?? 'J', 0, 1)) }}
                    </div>
                    <span>{{ $post->author->name ?? 'JodTod Team' }}</span>
                </div>
            @endif
            <span class="text-gray-300">|</span>
            <time datetime="{{ $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String() }}">
                {{ $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y') }}
            </time>
        </div>
    </div>
</section>

<!-- Article Content -->
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Featured Image -->
        @if($post->featured_image)
            <div class="mb-8 rounded-xl overflow-hidden">
                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-auto">
            </div>
        @endif

        <!-- Content -->
        <article class="prose prose-lg max-w-none text-gray-600 prose-headings:text-gray-900 prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline">
            {!! $post->content !!}
        </article>

        <!-- Share Section -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <p class="text-lg font-semibold text-gray-900">Share this article</p>
            <p class="mt-2 text-gray-600">Found this helpful? Share it with your friends and help them manage expenses better.</p>
        </div>

        <!-- Back to Blog -->
        <div class="mt-8">
            <a href="{{ url('/blog') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Blog
            </a>
        </div>

    </div>
</section>
@endsection
