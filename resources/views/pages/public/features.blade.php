@extends('layouts.public')

@section('title', 'Features - JodTod | Expense Tracker, Splitter & Task Planner')
@section('meta_description', 'Discover JodTod features: personal expense tracking, group expense splitting, smart settlement, to-do lists with reminders, task assignment, and event planning.')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl sm:text-5xl font-bold leading-tight">
                Powerful Features for <span class="text-primary-200">Money and Tasks</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-primary-100">
                Track personal spending, split group expenses, and plan every task with reminders — all in one simple app.
            </p>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Personal Expense Tracking -->
            <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Personal Expense Tracking</h3>
                <p class="mt-3 text-gray-600">Keep a detailed record of every rupee you spend. Know exactly where your money goes.</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Daily, weekly, and monthly tracking
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Customizable categories (Food, Travel, Shopping, etc.)
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Visual charts and category breakdowns
                    </li>
                </ul>
            </div>

            <!-- Group Expense Splitting -->
            <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Group Expense Splitting</h3>
                <p class="mt-3 text-gray-600">Split bills with friends, roommates, or family. Multiple split options for any situation.</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Equal split among all members
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Custom amount per person
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Percentage-based splitting
                    </li>
                </ul>
            </div>

            <!-- Smart Settlement -->
            <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Smart Settlement</h3>
                <p class="mt-3 text-gray-600">Our algorithm calculates the minimum number of transactions needed to settle all debts.</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Minimum transactions algorithm
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        One-click settle up
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Track settlement history
                    </li>
                </ul>
            </div>

            <!-- Tasks & Reminders -->
            <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Tasks &amp; Reminders</h3>
                <p class="mt-3 text-gray-600">Manage your daily and future to-dos in the same app you use for money. Never forget what needs to be done.</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Due dates and priorities for every task
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Categories like Home, Market, or your own
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Reminders before the deadline
                    </li>
                </ul>
            </div>

            <!-- Assign Tasks to Others -->
            <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Assign Tasks to Others</h3>
                <p class="mt-3 text-gray-600">Share the workload with family, friends, or teammates. Everyone knows exactly what they are responsible for.</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Assign any task to your contacts
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Assignees get notified instantly
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        See which tasks are pending or done
                    </li>
                </ul>
            </div>

            <!-- Event Planning -->
            <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Plan Big Events Early</h3>
                <p class="mt-3 text-gray-600">Weddings, trips, festivals, or house shifting — plan every detail weeks or months in advance.</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Schedule tasks months ahead
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Split the work across the whole family
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Track and split event costs together
                    </li>
                </ul>
            </div>

            <!-- Real-time Balances -->
            <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Real-time Balances</h3>
                <p class="mt-3 text-gray-600">Always know who owes whom. Balances update instantly when expenses are added.</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Instant balance calculation
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Clear who-owes-whom view
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Group and personal summaries
                    </li>
                </ul>
            </div>

            <!-- Secure & Private -->
            <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Secure & Private</h3>
                <p class="mt-3 text-gray-600">Your financial data stays yours. We take privacy and security seriously.</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Encrypted data storage
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        No data shared with third parties
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Phone verification for groups
                    </li>
                </ul>
            </div>

            <!-- Works Everywhere -->
            <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-bold text-gray-900">Works Everywhere</h3>
                <p class="mt-3 text-gray-600">Use JodTod on any device. Install it like a native app on your phone.</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Progressive Web App (PWA)
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Mobile, tablet, and desktop
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Installable on home screen
                    </li>
                </ul>
            </div>

        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 sm:py-24 bg-primary-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold">Ready to get started?</h2>
        <p class="mt-4 text-lg text-primary-100">Join thousands of users who manage their expenses and tasks with JodTod.</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/register') }}" class="inline-flex items-center justify-center px-8 py-3 rounded-lg bg-white text-primary-700 font-semibold hover:bg-primary-50 transition-colors">
                Get started for free
            </a>
            <a href="{{ url('/tools/expense-splitter') }}" class="inline-flex items-center justify-center px-8 py-3 rounded-lg border-2 border-white/30 text-white font-semibold hover:bg-white/10 transition-colors">
                Try Free Splitter Tool
            </a>
        </div>
    </div>
</section>
@endsection
