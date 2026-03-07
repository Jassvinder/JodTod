<?php

/*
|--------------------------------------------------------------------------
| Site Content Configuration
|--------------------------------------------------------------------------
|
| All static content, dummy data, and site-wide text is managed here.
| Change any value here and it reflects across the entire site.
|
| HOW TO USE IN BLADE:  {{ config('site.app.name') }}
| HOW TO USE IN PHP:    config('site.app.tagline')
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | App Info
    |--------------------------------------------------------------------------
    */
    'app' => [
        'name' => 'JodTod',
        'tagline' => 'Kharcha split karo, dosti mazboot rakho',
        'description' => 'Dosto, roommates aur family ke saath kharcha split karo asaani se. Personal expense tracking, group expense splitting, instant settlement.',
        'url' => env('APP_URL', 'http://localhost'),
        'currency' => '₹',
        'currency_code' => 'INR',
    ],

    /*
    |--------------------------------------------------------------------------
    | Landing Page Content
    |--------------------------------------------------------------------------
    */
    'landing' => [
        'hero' => [
            'title_line1' => 'Kharcha split karo,',
            'title_line2' => 'dosti mazboot rakho',
            'subtitle' => 'Group trip ho ya roommate ka kharcha - JodTod se track karo, split karo, aur settle karo. Kaun kisko kitna dega, sab clear!',
            'cta_primary' => 'Free me shuru karein',
            'cta_secondary' => 'Features dekhein',
        ],

        'features' => [
            [
                'title' => 'Personal Expense Tracking',
                'description' => 'Rozana ka kharcha track karo. Category-wise dekhein kitna kahan gaya.',
                'icon' => 'wallet',
                'color' => 'primary',
            ],
            [
                'title' => 'Group Expense Splitting',
                'description' => 'Friends, roommates ya family ke saath kharcha share karo. Equal, custom ya percentage split.',
                'icon' => 'users',
                'color' => 'success',
            ],
            [
                'title' => 'Smart Settlement',
                'description' => 'Kaun kisko kitna dega - sab automatic calculate. Minimum transactions me settle karo.',
                'icon' => 'calculator',
                'color' => 'accent',
            ],
        ],

        'how_it_works' => [
            [
                'step' => 1,
                'title' => 'Group Banao',
                'description' => 'Trip, flat, office lunch - kisi bhi cheez ke liye group create karo aur dosto ko add karo.',
            ],
            [
                'step' => 2,
                'title' => 'Kharcha Add Karo',
                'description' => 'Jisne bhi pay kiya wo expense add kare. Split type choose karo - equal, custom ya percentage.',
            ],
            [
                'step' => 3,
                'title' => 'Settle Up Karo',
                'description' => 'JodTod automatically batayega kaun kisko kitna dega. Minimum transactions, maximum clarity!',
            ],
        ],

        'cta' => [
            'title' => 'Paise ka jhagda band, JodTod shuru!',
            'subtitle' => 'Free me register karein aur abhi se expenses track karna shuru karein.',
            'button' => 'Abhi shuru karein - Free hai!',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation Links
    |--------------------------------------------------------------------------
    */
    'nav' => [
        'public' => [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Features', 'url' => '/features'],
            ['label' => 'Blog', 'url' => '/blog'],
            ['label' => 'Free Tools', 'url' => '/tools/expense-splitter'],
        ],
        'app' => [
            ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'home'],
            ['label' => 'Expenses', 'url' => '/expenses', 'icon' => 'wallet'],
            ['label' => 'Groups', 'url' => '/groups', 'icon' => 'users'],
            ['label' => 'Profile', 'url' => '/profile', 'icon' => 'user'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer Content
    |--------------------------------------------------------------------------
    */
    'footer' => [
        'description' => 'Kharcha split karo, dosti mazboot rakho.',
        'quick_links' => [
            ['label' => 'Features', 'url' => '/features'],
            ['label' => 'Blog', 'url' => '/blog'],
            ['label' => 'Free Tools', 'url' => '/tools/expense-splitter'],
        ],
        'legal_links' => [
            ['label' => 'Privacy Policy', 'url' => '/privacy'],
            ['label' => 'Terms of Service', 'url' => '/terms'],
            ['label' => 'Contact Us', 'url' => '/contact'],
        ],
        'other_links' => [
            ['label' => 'About Us', 'url' => '/about'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults
    |--------------------------------------------------------------------------
    */
    'seo' => [
        'default_title' => 'JodTod - Expense Tracker & Splitter | Kharcha Split Karo Asaani Se',
        'default_description' => 'JodTod - Dosto, roommates aur family ke saath kharcha split karo asaani se. Personal expense tracking, group expense splitting, instant settlement.',
        'og_image' => '/images/og-image.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Expense Categories (before DB seeder runs)
    |--------------------------------------------------------------------------
    */
    'categories' => [
        ['name' => 'Food', 'hindi' => 'Khana-Peena', 'icon' => 'utensils'],
        ['name' => 'Travel', 'hindi' => 'Safar', 'icon' => 'car'],
        ['name' => 'Shopping', 'hindi' => 'Khareedari', 'icon' => 'shopping-bag'],
        ['name' => 'Bills', 'hindi' => 'Bills', 'icon' => 'file-text'],
        ['name' => 'Entertainment', 'hindi' => 'Manoranjan', 'icon' => 'film'],
        ['name' => 'Medical', 'hindi' => 'Dawai-Doctor', 'icon' => 'heart-pulse'],
        ['name' => 'Education', 'hindi' => 'Padhai', 'icon' => 'book-open'],
        ['name' => 'Rent', 'hindi' => 'Kiraya', 'icon' => 'home'],
        ['name' => 'Other', 'hindi' => 'Anya', 'icon' => 'more-horizontal'],
    ],
];
