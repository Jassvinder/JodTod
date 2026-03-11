@extends('layouts.public')

@section('title', $page->meta_title ?: $page->title . ' - JodTod')
@section('meta_description', $page->meta_description ?: 'Learn more about ' . $page->title . ' on JodTod.')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl sm:text-5xl font-bold leading-tight">{{ $page->title }}</h1>
        </div>
    </div>
</section>

<!-- Content -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none text-gray-600">
            {!! $page->content !!}
        </div>
    </div>
</section>
@endsection
