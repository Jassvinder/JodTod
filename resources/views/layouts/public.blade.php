<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'JodTod - Expense Tracker & Splitter')</title>
    <meta name="description" content="@yield('meta_description', config('site.seo.default_description'))">

    @hasSection('meta_tags')
        @yield('meta_tags')
    @endif

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'JodTod - Expense Tracker & Splitter')">
    <meta property="og:description" content="@yield('meta_description', config('site.seo.default_description'))">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset(config('site.seo.og_image')))">
    <meta property="og:site_name" content="{{ config('site.app.name') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'JodTod - Expense Tracker & Splitter')">
    <meta name="twitter:description" content="@yield('meta_description', config('site.seo.default_description'))">
    <meta name="twitter:image" content="@yield('og_image', asset(config('site.seo.og_image')))">

    <!-- Schema / JSON-LD Structured Data -->
    @hasSection('schema')
        @yield('schema')
    @endif

    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#6366f1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <!-- Favicon & PWA -->
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="manifest" href="/manifest.json">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])

    <!-- CKEditor 5 content styles for blog/page rendering -->
    <style>
        .prose figure.image { margin: 1em 0; display: table; clear: both; }
        .prose figure.image img { display: block; max-width: 100%; height: auto; }
        .prose figure.image figcaption { display: table-caption; caption-side: bottom; font-size: 0.875em; color: #6b7280; padding: 0.5em 0; text-align: center; }
        .prose figure.image-style-align-left { float: left; margin-right: 1.5em; margin-bottom: 0.5em; }
        .prose figure.image-style-align-right { float: right; margin-left: 1.5em; margin-bottom: 0.5em; }
        .prose figure.image-style-align-center { margin-left: auto; margin-right: auto; }
        .prose figure.image.image_resized { max-width: 100%; }
        .prose figure.image.image_resized img { width: 100%; }
        .prose .image-style-side { float: right; margin-left: 1.5em; margin-bottom: 0.5em; max-width: 50%; }
        .prose figure.image-style-block-align-left { margin-right: auto; }
        .prose figure.image-style-block-align-right { margin-left: auto; }
        .prose .table table { border-collapse: collapse; width: 100%; }
        .prose .table table td, .prose .table table th { border: 1px solid #d1d5db; padding: 0.5em 0.75em; }
        .prose .table table th { background: #f3f4f6; font-weight: 600; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-gray-900">
    <!-- Header -->
    @include('components.blade.header')

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.blade.footer')

    @hasSection('scripts')
        @yield('scripts')
    @endif
</body>
</html>
