<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes (Blade - SEO optimized)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('pages.public.home');
})->name('home');

/*
|--------------------------------------------------------------------------
| App Routes (Inertia + Vue)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');
