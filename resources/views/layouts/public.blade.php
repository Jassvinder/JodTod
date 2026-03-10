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
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])
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
