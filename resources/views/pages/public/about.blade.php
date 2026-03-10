@extends('layouts.public')

@section('title', 'About JodTod - Our Story & Mission')
@section('meta_description', 'Learn about JodTod - born from the frustration of splitting bills after group trips. Our mission: make expense splitting transparent and fight-free.')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl sm:text-5xl font-bold leading-tight">About JodTod</h1>
            <p class="mt-6 text-lg sm:text-xl text-primary-100">
                Making expense splitting transparent and fight-free since 2026.
            </p>
        </div>
    </div>
</section>

<!-- Our Story -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Our Story</h2>
        </div>
        <div class="mt-8 prose prose-lg max-w-none text-gray-600">
            <p>
                JodTod was born from a frustration that most of us know too well — the awkward moment after a group trip
                when everyone tries to figure out who owes what. Spreadsheets get messy, mental math fails, and friendships
                get strained over a few hundred rupees.
            </p>
            <p class="mt-4">
                We built JodTod because we believed there had to be a better way. A way to track shared expenses in real time,
                split them fairly, and settle up with the minimum number of transactions. No more "I think you owe me..." conversations.
                No more lost receipts. No more money fights.
            </p>
            <p class="mt-4">
                What started as a simple tool for our own group trips has grown into a comprehensive expense management platform
                that handles both personal and group expenses with ease.
            </p>
        </div>
    </div>
</section>

<!-- Mission -->
<section class="py-16 sm:py-24 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Our Mission</h2>
        <p class="mt-6 text-xl text-gray-600 max-w-2xl mx-auto">
            Make expense splitting transparent and fight-free, so you can focus on the experiences and relationships that matter.
        </p>
    </div>
</section>

<!-- Values -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Our Values</h2>
            <p class="mt-4 text-lg text-gray-600">The principles that guide everything we build.</p>
        </div>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Transparency -->
            <div class="text-center p-8">
                <div class="w-16 h-16 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Transparency</h3>
                <p class="mt-3 text-gray-600">
                    Every expense, every split, every balance — crystal clear for everyone in the group. No hidden calculations, no surprises.
                </p>
            </div>

            <!-- Simplicity -->
            <div class="text-center p-8">
                <div class="w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Simplicity</h3>
                <p class="mt-3 text-gray-600">
                    Adding an expense should take seconds, not minutes. We obsess over making every interaction as simple as possible.
                </p>
            </div>

            <!-- Privacy -->
            <div class="text-center p-8">
                <div class="w-16 h-16 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Privacy</h3>
                <p class="mt-3 text-gray-600">
                    Your financial data is yours. We never sell or share your information. Security and privacy are built into every feature.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-16 sm:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">The Team</h2>
            <p class="mt-4 text-lg text-gray-600">A small team with a big mission.</p>
        </div>

        <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-3xl mx-auto">
            <!-- Team Member Placeholder -->
            <div class="text-center">
                <div class="w-24 h-24 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mx-auto text-3xl font-bold">
                    J
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">JodTod Team</h3>
                <p class="text-sm text-gray-500">Founder & Developer</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 sm:py-24 bg-primary-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold">Join the JodTod Community</h2>
        <p class="mt-4 text-lg text-primary-100">Start tracking and splitting expenses today — completely free.</p>
        <a href="{{ url('/register') }}" class="mt-8 inline-flex items-center justify-center px-8 py-3 rounded-lg bg-white text-primary-700 font-semibold hover:bg-primary-50 transition-colors">
            Get started for free
        </a>
    </div>
</section>
@endsection
