<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupExpenseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Blade - SEO optimized)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('pages.public.home');
})->name('home');

// Public pages
Route::get('/features', [PageController::class, 'features'])->name('features');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/tools/expense-splitter', [PageController::class, 'splitter'])->name('tools.splitter');

// Blog (public, Blade for SEO)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Offline fallback page (for PWA service worker)
Route::get('/offline', function () {
    return view('pages.public.offline');
})->name('offline');

/*
|--------------------------------------------------------------------------
| App Routes (Inertia + Vue - Auth required)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Personal Expenses
    Route::resource('expenses', ExpenseController::class)
        ->except(['show']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences');
});

// Groups (require phone verification)
Route::middleware(['auth', 'verified', 'phone.verified'])->group(function () {
    Route::resource('groups', GroupController::class);
    Route::post('/groups/{group}/invite', [GroupController::class, 'refreshInviteCode'])->name('groups.invite');
    Route::post('/groups/join', [GroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');
    Route::delete('/groups/{group}/members/{userId}', [GroupController::class, 'removeMember'])->name('groups.members.remove');
    Route::get('/groups/{group}/search-users', [GroupController::class, 'searchUsers'])->name('groups.search-users');
    Route::post('/groups/{group}/add-member', [GroupController::class, 'addMember'])->name('groups.add-member');

    // Group Expenses
    Route::get('/groups/{group}/expenses', [GroupExpenseController::class, 'index'])->name('groups.expenses.index');
    Route::get('/groups/{group}/expenses/create', [GroupExpenseController::class, 'create'])->name('groups.expenses.create');
    Route::post('/groups/{group}/expenses', [GroupExpenseController::class, 'store'])->name('groups.expenses.store');
    Route::get('/groups/{group}/expenses/{expense}/edit', [GroupExpenseController::class, 'edit'])->name('groups.expenses.edit');
    Route::put('/groups/{group}/expenses/{expense}', [GroupExpenseController::class, 'update'])->name('groups.expenses.update');
    Route::delete('/groups/{group}/expenses/{expense}', [GroupExpenseController::class, 'destroy'])->name('groups.expenses.destroy');
    Route::get('/groups/{group}/balances', [GroupExpenseController::class, 'balances'])->name('groups.balances');

    // Settlements
    Route::get('/groups/{group}/settlements', [SettlementController::class, 'index'])->name('groups.settlements.index');
    Route::post('/groups/{group}/settle', [SettlementController::class, 'settle'])->name('groups.settle');
    Route::put('/groups/{group}/settlements/{settlement}/complete', [SettlementController::class, 'markCompleted'])->name('groups.settlements.complete');
    Route::post('/groups/{group}/settle-all', [SettlementController::class, 'settleAll'])->name('groups.settle-all');
});

// Public join link (auth required but not verified - so user can verify after)
Route::middleware('auth')->group(function () {
    Route::get('/join/{inviteCode}', [GroupController::class, 'joinViaLink'])->name('groups.join.link');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Phone verification
    Route::post('/profile/phone/send-otp', [PhoneVerificationController::class, 'send'])->name('profile.phone.send');
    Route::post('/profile/phone/verify', [PhoneVerificationController::class, 'verify'])->name('profile.phone.verify');
    Route::delete('/profile/phone', [PhoneVerificationController::class, 'remove'])->name('profile.phone.remove');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Auth + Verified + Admin role required)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::put('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Category management
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // Blog management
    Route::get('/blog', [AdminBlogController::class, 'index'])->name('admin.blog');
    Route::get('/blog/create', [AdminBlogController::class, 'create'])->name('admin.blog.create');
    Route::post('/blog', [AdminBlogController::class, 'store'])->name('admin.blog.store');
    Route::get('/blog/{post}/edit', [AdminBlogController::class, 'edit'])->name('admin.blog.edit');
    Route::put('/blog/{post}', [AdminBlogController::class, 'update'])->name('admin.blog.update');
    Route::delete('/blog/{post}', [AdminBlogController::class, 'destroy'])->name('admin.blog.destroy');
});

require __DIR__.'/auth.php';
