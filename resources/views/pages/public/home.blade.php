@extends('layouts.public')

@section('title', config('site.seo.default_title'))
@section('meta_description', config('site.seo.default_description'))

@section('content')
@php
    $hero = config('site.landing.hero');
    $features = config('site.landing.features');
    // Literal class names so Tailwind can detect them at build time.
    $iconColors = [
        'primary' => 'bg-primary-100 text-primary-600',
        'success' => 'bg-success-100 text-success-600',
        'accent' => 'bg-accent-100 text-accent-600',
    ];
    $planning = config('site.landing.event_planning');
    $steps = config('site.landing.how_it_works');
    $cta = config('site.landing.cta');
@endphp
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
        <div class="max-w-3xl">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight">
                {{ $hero['title_line1'] }}<br>
                <span class="text-primary-200">{{ $hero['title_line2'] }}</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-primary-100 max-w-2xl">
                {{ $hero['subtitle'] }}
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <a href="{{ url('/register') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-white text-primary-700 font-semibold hover:bg-primary-50 transition-colors">
                    {{ $hero['cta_primary'] }}
                </a>
                <a href="{{ url('/features') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-lg border-2 border-white/30 text-white font-semibold hover:bg-white/10 transition-colors">
                    {{ $hero['cta_secondary'] }}
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Everything in one place</h2>
            <p class="mt-4 text-lg text-gray-600">Track expenses, split with groups, and plan every task — {{ config('site.app.name') }} handles it all</p>
        </div>

        <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($features as $feature)
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="w-12 h-12 rounded-lg {{ $iconColors[$feature['color']] ?? $iconColors['primary'] }} flex items-center justify-center">
                    @if($feature['icon'] === 'wallet')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    @elseif($feature['icon'] === 'users')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    @elseif($feature['icon'] === 'calculator')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    @elseif($feature['icon'] === 'check')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    @elseif($feature['icon'] === 'user-plus')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    @elseif($feature['icon'] === 'calendar')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    @endif
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $feature['title'] }}</h3>
                <p class="mt-2 text-gray-600">{{ $feature['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Event Planning Section -->
<section class="py-16 sm:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $planning['title'] }}</h2>
                <p class="mt-4 text-lg text-gray-600">{{ $planning['subtitle'] }}</p>
                <ul class="mt-6 space-y-3">
                    @foreach($planning['points'] as $point)
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        {{ $point }}
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $planning['sample_title'] }}</h3>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-primary-100 text-primary-700">{{ count($planning['sample_tasks']) }} tasks</span>
                </div>
                <ul class="mt-4 divide-y divide-gray-100">
                    @foreach($planning['sample_tasks'] as $task)
                    <li class="flex items-center gap-3 py-3">
                        <span class="w-5 h-5 rounded-full border-2 flex-shrink-0 flex items-center justify-center {{ $task['done'] ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300' }}">
                            @if($task['done'])
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium truncate {{ $task['done'] ? 'text-gray-400 line-through' : 'text-gray-900' }}">{{ $task['title'] }}</p>
                            <p class="text-xs text-gray-500">Assigned to {{ $task['assignee'] }}</p>
                        </div>
                        <span class="text-xs text-gray-500 whitespace-nowrap">{{ $task['due'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">How it works</h2>
            <p class="mt-4 text-lg text-gray-600">Just 3 simple steps</p>
        </div>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($steps as $step)
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-primary-600 text-white text-2xl font-bold flex items-center justify-center mx-auto">{{ $step['step'] }}</div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $step['title'] }}</h3>
                <p class="mt-2 text-gray-600">{{ $step['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 sm:py-24 bg-primary-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold">{{ $cta['title'] }}</h2>
        <p class="mt-4 text-lg text-primary-100">{{ $cta['subtitle'] }}</p>
        <a href="{{ url('/register') }}" class="mt-8 inline-flex items-center justify-center px-8 py-3 rounded-lg bg-white text-primary-700 font-semibold hover:bg-primary-50 transition-colors">
            {{ $cta['button'] }}
        </a>
    </div>
</section>
@endsection
