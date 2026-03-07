@extends('layouts.public')

@section('title', 'JodTod - Expense Tracker & Splitter | Kharcha Split Karo Asaani Se')
@section('meta_description', 'JodTod - Dosto, roommates aur family ke saath kharcha split karo asaani se. Personal expense tracking, group expense splitting, instant settlement.')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
        <div class="max-w-3xl">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight">
                Kharcha split karo,<br>
                <span class="text-primary-200">dosti mazboot rakho</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-primary-100 max-w-2xl">
                Group trip ho ya roommate ka kharcha - JodTod se track karo, split karo, aur settle karo. Kaun kisko kitna dega, sab clear!
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <a href="/register" class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-white text-primary-700 font-semibold hover:bg-primary-50 transition-colors">
                    Free me shuru karein
                </a>
                <a href="/features" class="inline-flex items-center justify-center px-6 py-3 rounded-lg border-2 border-white/30 text-white font-semibold hover:bg-white/10 transition-colors">
                    Features dekhein
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Sab kuch ek jagah</h2>
            <p class="mt-4 text-lg text-gray-600">Personal expenses track karo ya group me split karo - JodTod sab sambhalta hai</p>
        </div>

        <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="w-12 h-12 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Personal Expense Tracking</h3>
                <p class="mt-2 text-gray-600">Rozana ka kharcha track karo. Category-wise dekhein kitna kahan gaya.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="w-12 h-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Group Expense Splitting</h3>
                <p class="mt-2 text-gray-600">Friends, roommates ya family ke saath kharcha share karo. Equal, custom ya percentage split.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="w-12 h-12 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Smart Settlement</h3>
                <p class="mt-2 text-gray-600">Kaun kisko kitna dega - sab automatic calculate. Minimum transactions me settle karo.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 sm:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Kaise kaam karta hai?</h2>
            <p class="mt-4 text-lg text-gray-600">Bas 3 simple steps</p>
        </div>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-primary-600 text-white text-2xl font-bold flex items-center justify-center mx-auto">1</div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Group Banao</h3>
                <p class="mt-2 text-gray-600">Trip, flat, office lunch - kisi bhi cheez ke liye group create karo aur dosto ko add karo.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-primary-600 text-white text-2xl font-bold flex items-center justify-center mx-auto">2</div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Kharcha Add Karo</h3>
                <p class="mt-2 text-gray-600">Jisne bhi pay kiya wo expense add kare. Split type choose karo - equal, custom ya percentage.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-primary-600 text-white text-2xl font-bold flex items-center justify-center mx-auto">3</div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Settle Up Karo</h3>
                <p class="mt-2 text-gray-600">JodTod automatically batayega kaun kisko kitna dega. Minimum transactions, maximum clarity!</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 sm:py-24 bg-primary-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold">Paise ka jhagda band, JodTod shuru!</h2>
        <p class="mt-4 text-lg text-primary-100">Free me register karein aur abhi se expenses track karna shuru karein.</p>
        <a href="/register" class="mt-8 inline-flex items-center justify-center px-8 py-3 rounded-lg bg-white text-primary-700 font-semibold hover:bg-primary-50 transition-colors">
            Abhi shuru karein - Free hai!
        </a>
    </div>
</section>
@endsection
