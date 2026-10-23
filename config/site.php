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
        'tagline' => 'Track expenses, split bills, get things done',
        'description' => 'Track personal expenses, split group bills, and plan every task with friends and family. Assign to-dos, set reminders, and settle up in one app.',
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
            'title_line1' => 'Track. Split. Plan.',
            'title_line2' => 'Get things done together',
            'subtitle' => 'From daily spending to a wedding months away — track expenses, split bills with your group, and plan every task with reminders. Assign work to family and friends, and never miss a thing.',
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
            [
                'title' => 'Tasks & Reminders',
                'description' => 'Manage daily and future to-dos with due dates, priorities, categories and timely reminders.',
                'icon' => 'check',
                'color' => 'primary',
            ],
            [
                'title' => 'Assign Tasks to Others',
                'description' => 'Hand off tasks to family, friends or teammates. They get notified and you see when it is done.',
                'icon' => 'user-plus',
                'color' => 'success',
            ],
            [
                'title' => 'Plan Big Events Early',
                'description' => 'Weddings, trips, house shifting — list every task months ahead and handle the costs in the same app.',
                'icon' => 'calendar',
                'color' => 'accent',
            ],
        ],

        'event_planning' => [
            'title' => 'Plan big events, months ahead',
            'subtitle' => 'A wedding, a family trip or moving to a new home needs hundreds of small tasks. List them all early, share the work, and keep the budget in the same place.',
            'points' => [
                'Add tasks with due dates weeks or months in advance',
                'Assign each task to the right person and get notified when it is done',
                'Get reminders before deadlines so nothing is missed',
                'Track and split every event expense with the same group',
            ],
            'sample_title' => 'Sister\'s Wedding',
            'sample_tasks' => [
                ['title' => 'Book the banquet hall', 'assignee' => 'You', 'due' => 'Done', 'done' => true],
                ['title' => 'Finalise the caterer menu', 'assignee' => 'Rahul', 'due' => 'In 2 weeks', 'done' => false],
                ['title' => 'Send invitation cards', 'assignee' => 'Priya', 'due' => 'In 1 month', 'done' => false],
                ['title' => 'Arrange guest transport', 'assignee' => 'Amit', 'due' => 'In 2 months', 'done' => false],
            ],
        ],

        'how_it_works' => [
            [
                'step' => 1,
                'title' => 'Create a Group',
                'description' => 'Trip, flat, family event — create a group for anything and add the people involved.',
            ],
            [
                'step' => 2,
                'title' => 'Add Expenses & Tasks',
                'description' => 'Log who paid and how to split it. Add the to-dos, set due dates and reminders, and assign them.',
            ],
            [
                'step' => 3,
                'title' => 'Settle Up & Get It Done',
                'description' => 'JodTod shows who owes whom and which tasks are still pending. Nothing slips through the cracks.',
            ],
        ],

        'cta' => [
            'title' => 'No more money fights or missed tasks — start with JodTod!',
            'subtitle' => 'Register for free and start tracking expenses and planning tasks today.',
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
            ['label' => 'Tasks', 'url' => '/todos', 'icon' => 'check'],
            ['label' => 'Profile', 'url' => '/profile', 'icon' => 'user'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer Content
    |--------------------------------------------------------------------------
    */
    'footer' => [
        'description' => 'Track expenses, split bills, and plan tasks together.',
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
    | Support JodTod (free-for-now note + UPI donations)
    |--------------------------------------------------------------------------
    |
    | Shown on the web Support page, the landing page and the mobile app
    | (via GET /api/v1/support). The UPI ID, payee name and QR image are
    | set from Admin > Settings, not here.
    |
    */
    'support' => [
        'title' => 'JodTod is free to use',
        'paragraphs' => [
            'Running JodTod has real costs: servers, notifications, emails and ongoing maintenance. Right now every feature is free for everyone.',
            'In the future we may add an optional donation or a small subscription, depending on how the app grows. Some features will always stay free.',
            'If you find JodTod useful, you can support it today with any amount through UPI. Every contribution helps keep the app running.',
        ],
        'early_users_note' => 'Early users will be rewarded when that happens.',
        'pay_steps' => [
            'Save the QR code to your gallery.',
            'Open any UPI app (Google Pay, PhonePe, Paytm, BHIM).',
            'Scan the QR from your gallery, or pay to the UPI ID below.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults
    |--------------------------------------------------------------------------
    */
    'seo' => [
        'default_title' => 'JodTod - Expense Tracker, Bill Splitter & Task Planner',
        'default_description' => 'JodTod - Track personal expenses, split group bills, and plan tasks with reminders. Assign to-dos to friends and family and manage big events months ahead.',
        'og_image' => '/images/og-image.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Expense Categories (before DB seeder runs)
    |--------------------------------------------------------------------------
    */
    'categories' => [
        ['name' => 'Food', 'icon' => '🍽️'],
        ['name' => 'Travel', 'icon' => '🚗'],
        ['name' => 'Shopping', 'icon' => '🛍️'],
        ['name' => 'Bills', 'icon' => '📄'],
        ['name' => 'Entertainment', 'icon' => '🎬'],
        ['name' => 'Medical', 'icon' => '🏥'],
        ['name' => 'Education', 'icon' => '📚'],
        ['name' => 'Rent', 'icon' => '🏠'],
        ['name' => 'Other', 'icon' => '📦'],
    ],
];
