<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'JodTod - Expense Tracker & Splitter')</title>
    <meta name="description" content="@yield('meta_description', 'JodTod - Dosto ke saath kharcha split karo asaani se. Track personal & group expenses, settle up instantly.')">

    @hasSection('meta_tags')
        @yield('meta_tags')
    @endif

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'JodTod - Expense Tracker & Splitter')">
    <meta property="og:description" content="@yield('meta_description', 'JodTod - Dosto ke saath kharcha split karo asaani se.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="canonical" href="{{ url()->current() }}">
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
