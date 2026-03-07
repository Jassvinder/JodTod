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
        'tagline' => 'Split expenses, strengthen friendships',
        'description' => 'Split expenses with friends, roommates and family easily. Personal expense tracking, group expense splitting, instant settlement.',
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
            'title_line1' => 'Split expenses,',
            'title_line2' => 'strengthen friendships',
            'subtitle' => 'Whether it\'s a group trip or roommate bills — track, split, and settle with JodTod. Who owes whom, all crystal clear!',
            'cta_primary' => 'Get started for free',
            'cta_secondary' => 'See features',
        ],

        'features' => [
            [
                'title' => 'Personal Expense Tracking',
                'description' => 'Track your daily expenses. See category-wise breakdown of where your money goes.',
                'icon' => 'wallet',
                'color' => 'primary',
            ],
            [
                'title' => 'Group Expense Splitting',
                'description' => 'Share expenses with friends, roommates or family. Equal, custom or percentage split.',
                'icon' => 'users',
                'color' => 'success',
            ],
            [
                'title' => 'Smart Settlement',
                'description' => 'Who owes whom — all calculated automatically. Settle in minimum transactions.',
                'icon' => 'calculator',
                'color' => 'accent',
            ],
        ],

        'how_it_works' => [
            [
                'step' => 1,
                'title' => 'Create a Group',
                'description' => 'Trip, flat, office lunch — create a group for anything and add your friends.',
            ],
            [
                'step' => 2,
                'title' => 'Add Expenses',
                'description' => 'Whoever paid adds the expense. Choose split type — equal, custom or percentage.',
            ],
            [
                'step' => 3,
                'title' => 'Settle Up',
                'description' => 'JodTod automatically shows who owes whom. Minimum transactions, maximum clarity!',
            ],
        ],

        'cta' => [
            'title' => 'No more money fights, start with JodTod!',
            'subtitle' => 'Register for free and start tracking your expenses today.',
            'button' => 'Get started — It\'s free!',
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
        'description' => 'Split expenses, strengthen friendships.',
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
        'default_title' => 'JodTod - Expense Tracker & Splitter | Split Expenses Easily',
        'default_description' => 'JodTod - Split expenses with friends, roommates and family easily. Personal expense tracking, group expense splitting, instant settlement.',
        'og_image' => '/images/og-image.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Expense Categories (before DB seeder runs)
    |--------------------------------------------------------------------------
    */
    'categories' => [
        ['name' => 'Food', 'icon' => 'utensils'],
        ['name' => 'Travel', 'icon' => 'car'],
        ['name' => 'Shopping', 'icon' => 'shopping-bag'],
        ['name' => 'Bills', 'icon' => 'file-text'],
        ['name' => 'Entertainment', 'icon' => 'film'],
        ['name' => 'Medical', 'icon' => 'heart-pulse'],
        ['name' => 'Education', 'icon' => 'book-open'],
        ['name' => 'Rent', 'icon' => 'home'],
        ['name' => 'Other', 'icon' => 'more-horizontal'],
    ],
];
