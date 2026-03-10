@extends('layouts.public')

@section('title', 'Free Expense Splitter Calculator - JodTod')
@section('meta_description', 'Split expenses between friends instantly. Free online expense splitter calculator. Equal or custom splits with no sign-up required.')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-3xl sm:text-4xl font-bold">Free Expense Splitter Calculator</h1>
            <p class="mt-4 text-lg text-primary-100">Split expenses between friends instantly. No sign-up required.</p>
        </div>
    </div>
</section>

<!-- Splitter Tool -->
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8" x-data="splitterTool()">

        <!-- Tool Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            <!-- Amount Input -->
            <div class="p-6 border-b border-gray-100">
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg font-medium">{{ config('site.app.currency') }}</span>
                    <input
                        type="number"
                        x-model.number="amount"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        class="w-full pl-10 pr-4 py-3 rounded-lg border-gray-300 text-2xl font-bold text-gray-900 focus:border-primary-500 focus:ring-primary-500"
                    >
                </div>
            </div>

            <!-- Split Type Toggle -->
            <div class="p-6 border-b border-gray-100">
                <label class="block text-sm font-medium text-gray-700 mb-3">Split Type</label>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button
                        type="button"
                        @click="splitType = 'equal'"
                        :class="splitType === 'equal' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-2.5 px-4 rounded-md text-sm font-semibold transition-all"
                    >
                        Equal Split
                    </button>
                    <button
                        type="button"
                        @click="splitType = 'custom'"
                        :class="splitType === 'custom' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-2.5 px-4 rounded-md text-sm font-semibold transition-all"
                    >
                        Custom Split
                    </button>
                </div>
            </div>

            <!-- People Section -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-gray-700">People</label>
                    <button
                        type="button"
                        @click="addPerson()"
                        class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Person
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(person, index) in people" :key="index">
                        <div class="flex items-center gap-3">
                            <!-- Person Name -->
                            <div class="flex-1">
                                <input
                                    type="text"
                                    x-model="person.name"
                                    :placeholder="'Person ' + (index + 1)"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                            </div>

                            <!-- Custom Amount (shown only in custom mode) -->
                            <div x-show="splitType === 'custom'" class="w-32">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">{{ config('site.app.currency') }}</span>
                                    <input
                                        type="number"
                                        x-model.number="person.share"
                                        min="0"
                                        step="0.01"
                                        placeholder="0"
                                        class="w-full pl-7 pr-3 py-2 rounded-lg border-gray-300 text-sm font-medium focus:border-primary-500 focus:ring-primary-500"
                                    >
                                </div>
                            </div>

                            <!-- Remove Button -->
                            <button
                                type="button"
                                @click="removePerson(index)"
                                x-show="people.length > 2"
                                class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Custom Split Remaining -->
                <div x-show="splitType === 'custom' && amount > 0" class="mt-4 p-3 rounded-lg" :class="remaining() === 0 ? 'bg-green-50 border border-green-200' : 'bg-amber-50 border border-amber-200'">
                    <div class="flex items-center justify-between text-sm">
                        <span :class="remaining() === 0 ? 'text-green-700' : 'text-amber-700'" class="font-medium">
                            <span x-show="remaining() === 0">All allocated!</span>
                            <span x-show="remaining() > 0">Remaining to allocate:</span>
                            <span x-show="remaining() < 0">Over-allocated by:</span>
                        </span>
                        <span :class="remaining() === 0 ? 'text-green-700' : 'text-amber-700'" class="font-bold" x-show="remaining() !== 0">
                            {{ config('site.app.currency') }}<span x-text="Math.abs(remaining()).toFixed(2)"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div x-show="amount > 0 && people.length >= 2" class="p-6 bg-gradient-to-br from-primary-50 to-primary-100/50">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Split Result</h3>

                <!-- Equal Split Results -->
                <div x-show="splitType === 'equal'" class="space-y-3">
                    <template x-for="(person, index) in people" :key="'result-' + index">
                        <div class="flex items-center justify-between bg-white rounded-lg p-4 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-bold"
                                     x-text="(person.name || ('P' + (index + 1))).charAt(0).toUpperCase()">
                                </div>
                                <span class="font-medium text-gray-900" x-text="person.name || ('Person ' + (index + 1))"></span>
                            </div>
                            <span class="text-lg font-bold text-primary-600">
                                {{ config('site.app.currency') }}<span x-text="perPerson().toFixed(2)"></span>
                            </span>
                        </div>
                    </template>

                    <div class="mt-4 text-center text-sm text-gray-500">
                        {{ config('site.app.currency') }}<span x-text="amount.toFixed(2)"></span> split equally between <span x-text="people.length"></span> people
                    </div>
                </div>

                <!-- Custom Split Results -->
                <div x-show="splitType === 'custom'" class="space-y-3">
                    <template x-for="(person, index) in people" :key="'custom-result-' + index">
                        <div class="flex items-center justify-between bg-white rounded-lg p-4 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-bold"
                                     x-text="(person.name || ('P' + (index + 1))).charAt(0).toUpperCase()">
                                </div>
                                <span class="font-medium text-gray-900" x-text="person.name || ('Person ' + (index + 1))"></span>
                            </div>
                            <span class="text-lg font-bold text-primary-600">
                                {{ config('site.app.currency') }}<span x-text="(person.share || 0).toFixed(2)"></span>
                            </span>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- CTA Section -->
        <div class="mt-12 text-center bg-gray-50 rounded-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900">Want more features?</h2>
            <p class="mt-3 text-gray-600">
                Track expenses over time, manage groups, get smart settlement suggestions, and more. Sign up for free!
            </p>
            <a href="{{ url('/register') }}" class="mt-6 inline-flex items-center justify-center px-8 py-3 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors">
                Sign up for free
            </a>
        </div>

    </div>
</section>

<!-- How It Helps -->
<section class="py-12 sm:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Why Use This Splitter?</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-14 h-14 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Instant Results</h3>
                <p class="mt-2 text-gray-600">Enter the amount, add people, and get instant split calculations. No waiting, no sign-up.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 rounded-xl bg-green-100 text-green-600 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Equal or Custom</h3>
                <p class="mt-2 text-gray-600">Split equally among everyone, or set custom amounts for each person.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">100% Free & Private</h3>
                <p class="mt-2 text-gray-600">No sign-up required. Your data stays in your browser. Nothing is stored on our servers.</p>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function splitterTool() {
    return {
        amount: 0,
        splitType: 'equal',
        people: [
            { name: '', share: 0 },
            { name: '', share: 0 },
        ],

        addPerson() {
            this.people.push({ name: '', share: 0 });
        },

        removePerson(index) {
            if (this.people.length > 2) {
                this.people.splice(index, 1);
            }
        },

        perPerson() {
            if (this.people.length === 0) return 0;
            return this.amount / this.people.length;
        },

        remaining() {
            const totalShares = this.people.reduce((sum, p) => sum + (p.share || 0), 0);
            return this.amount - totalShares;
        },
    };
}
</script>
@endsection
