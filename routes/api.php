<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\DeviceTokenController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\GroupExpenseController;
use App\Http\Controllers\Api\V1\IncomeController;
use App\Http\Controllers\Api\V1\LedgerController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OtpController;
use App\Http\Controllers\Api\V1\RememberItemController;
use App\Http\Controllers\Api\V1\SettlementController;
use App\Http\Controllers\Api\V1\SupportController;
use App\Http\Controllers\Api\V1\TeamBuilder\ProspectController;
use App\Http\Controllers\Api\V1\TeamBuilder\TeamController;
use App\Http\Controllers\Api\V1\TodoCategoryController;
use App\Http\Controllers\Api\V1\TodoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Mobile app API endpoints. Auth via Laravel Sanctum tokens.
| All routes prefixed with /api/v1/
|
*/

Route::prefix('v1')->group(function () {

    // Public routes (no auth required, rate limited)
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/auth/google', [AuthController::class, 'googleLogin']);
    });
    Route::middleware('throttle:3,1')->group(function () {
        Route::post('/otp/send', [OtpController::class, 'send']);
        Route::post('/otp/verify', [OtpController::class, 'verify']);
    });

    // Protected routes (Sanctum token auth + banned check)
    Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckBanned::class])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/user/app-mode', [AuthController::class, 'updateAppMode']);
        Route::post('/email/verification-notification', [AuthController::class, 'resendVerification']);

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit']);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::delete('/profile', [ProfileController::class, 'destroy']);
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
        Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar']);
        Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update']);
        Route::post('/profile/phone/send-otp', [\App\Http\Controllers\PhoneVerificationController::class, 'send']);
        Route::post('/profile/phone/verify', [\App\Http\Controllers\PhoneVerificationController::class, 'verify']);
        Route::delete('/profile/phone', [\App\Http\Controllers\PhoneVerificationController::class, 'remove']);

        // Expenses
        Route::get('/expenses', [ExpenseController::class, 'index']);
        Route::post('/expenses', [ExpenseController::class, 'store']);
        Route::get('/expenses/{expense}', [ExpenseController::class, 'show']);
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update']);
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);
        Route::get('/expense-suggestions', [ExpenseController::class, 'suggestions']);

        // Incomes
        Route::get('/incomes', [IncomeController::class, 'index']);
        Route::post('/incomes', [IncomeController::class, 'store']);
        Route::get('/incomes/{income}', [IncomeController::class, 'show']);
        Route::put('/incomes/{income}', [IncomeController::class, 'update']);
        Route::delete('/incomes/{income}', [IncomeController::class, 'destroy']);
        Route::get('/income-suggestions', [IncomeController::class, 'suggestions']);

        // Contacts
        Route::get('/contacts', [ContactController::class, 'index']);
        Route::get('/contacts/search', [ContactController::class, 'search']);
        Route::get('/contacts/requests', [ContactController::class, 'requests']);
        Route::get('/contacts/requests/sent', [ContactController::class, 'sentRequests']);
        Route::post('/contacts', [ContactController::class, 'store']);
        Route::post('/contacts/{contact}/approve', [ContactController::class, 'approve']);
        Route::delete('/contacts/{contact}/reject', [ContactController::class, 'reject']);
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);

        // Categories
        Route::get('/categories', [CategoryController::class, 'index']);

        // Support JodTod (free-for-now note + UPI details)
        Route::get('/support', [SupportController::class, 'show']);

        // Todos
        Route::get('/todos', [TodoController::class, 'index']);
        Route::post('/todos', [TodoController::class, 'store']);
        Route::get('/todos/{todo}', [TodoController::class, 'show']);
        Route::put('/todos/{todo}', [TodoController::class, 'update']);
        Route::delete('/todos/{todo}', [TodoController::class, 'destroy']);
        Route::patch('/todos/{todo}/toggle', [TodoController::class, 'toggle']);

        // Todo Categories
        Route::get('/todo-categories', [TodoCategoryController::class, 'index']);
        Route::post('/todo-categories', [TodoCategoryController::class, 'store']);
        Route::put('/todo-categories/{todoCategory}', [TodoCategoryController::class, 'update']);
        Route::delete('/todo-categories/{todoCategory}', [TodoCategoryController::class, 'destroy']);

        // Remember items
        Route::get('/remember-items', [RememberItemController::class, 'index']);
        Route::post('/remember-items', [RememberItemController::class, 'store']);
        Route::get('/remember-items/{rememberItem}', [RememberItemController::class, 'show']);
        Route::put('/remember-items/{rememberItem}', [RememberItemController::class, 'update']);
        Route::delete('/remember-items/{rememberItem}', [RememberItemController::class, 'destroy']);
        Route::post('/remember-items/{rememberItem}/entries', [RememberItemController::class, 'storeEntry']);
        Route::delete('/remember-items/{rememberItem}/entries/{rememberEntry}', [RememberItemController::class, 'destroyEntry']);

        // Lend & Borrow (personal ledger)
        Route::get('/ledger-people', [LedgerController::class, 'index']);
        Route::post('/ledger-people', [LedgerController::class, 'store']);
        Route::get('/ledger-people/{ledgerPerson}', [LedgerController::class, 'show']);
        Route::put('/ledger-people/{ledgerPerson}', [LedgerController::class, 'update']);
        Route::delete('/ledger-people/{ledgerPerson}', [LedgerController::class, 'destroy']);
        Route::post('/ledger-people/{ledgerPerson}/entries', [LedgerController::class, 'storeEntry']);
        Route::put('/ledger-people/{ledgerPerson}/entries/{ledgerEntry}', [LedgerController::class, 'updateEntry']);
        Route::delete('/ledger-people/{ledgerPerson}/entries/{ledgerEntry}', [LedgerController::class, 'destroyEntry']);

        // Team Builder (network marketing teams, prospects and follow-ups)
        Route::prefix('team-builder')->group(function () {
            Route::get('/dashboard', [ProspectController::class, 'dashboard']);
            Route::get('/follow-ups', [ProspectController::class, 'followUps']);

            Route::get('/teams', [TeamController::class, 'index']);
            Route::post('/teams', [TeamController::class, 'store']);
            Route::post('/teams/join', [TeamController::class, 'join']);
            Route::get('/teams/{team}', [TeamController::class, 'show']);
            Route::put('/teams/{team}', [TeamController::class, 'update']);
            Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
            Route::post('/teams/{team}/leave', [TeamController::class, 'leave']);

            Route::get('/prospects', [ProspectController::class, 'index']);
            Route::post('/prospects', [ProspectController::class, 'store']);
            Route::get('/prospects/{prospect}', [ProspectController::class, 'show']);
            Route::put('/prospects/{prospect}', [ProspectController::class, 'update']);
            Route::delete('/prospects/{prospect}', [ProspectController::class, 'destroy']);
            Route::post('/prospects/{prospect}/activities', [ProspectController::class, 'storeActivity']);
            Route::delete('/prospects/{prospect}/activities/{activity}', [ProspectController::class, 'destroyActivity']);
        });

        // Groups
        Route::get('/groups', [GroupController::class, 'index']);
        Route::post('/groups', [GroupController::class, 'store']);
        Route::get('/groups/{group}', [GroupController::class, 'show']);
        Route::put('/groups/{group}', [GroupController::class, 'update']);
        Route::delete('/groups/{group}', [GroupController::class, 'destroy']);
        Route::post('/groups/join', [GroupController::class, 'join']);
        Route::post('/groups/{group}/leave', [GroupController::class, 'leave']);
        Route::post('/groups/{group}/add-member', [GroupController::class, 'addMember']);
        Route::post('/groups/{group}/guest-members', [GroupController::class, 'addGuestMember']);
        Route::delete('/groups/{group}/members/{userId}', [GroupController::class, 'removeMember']);
        Route::post('/groups/{group}/members/{userId}/reactivate', [GroupController::class, 'reactivateMember']);
        Route::post('/groups/{group}/members/{userId}/approve', [GroupController::class, 'approveMember']);
        Route::delete('/groups/{group}/members/{userId}/reject', [GroupController::class, 'rejectMember']);

        // Group Expenses
        Route::get('/groups/{group}/expenses', [GroupExpenseController::class, 'index']);
        Route::post('/groups/{group}/expenses', [GroupExpenseController::class, 'store']);
        Route::get('/groups/{group}/expenses/{expense}', [GroupExpenseController::class, 'show']);
        Route::put('/groups/{group}/expenses/{expense}', [GroupExpenseController::class, 'update']);
        Route::delete('/groups/{group}/expenses/{expense}', [GroupExpenseController::class, 'destroy']);
        Route::get('/groups/{group}/balances', [GroupExpenseController::class, 'balances']);

        // Settlements
        Route::get('/groups/{group}/settlements', [SettlementController::class, 'index']);
        Route::post('/groups/{group}/settle', [SettlementController::class, 'settle']);
        Route::put('/groups/{group}/settlements/{settlement}/complete', [SettlementController::class, 'markCompleted']);
        Route::post('/groups/{group}/settle-all', [SettlementController::class, 'settleAll']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/recent', [NotificationController::class, 'recent']);
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

        // Device Tokens (Push Notifications)
        Route::post('/device-token', [DeviceTokenController::class, 'store']);
        Route::delete('/device-token', [DeviceTokenController::class, 'destroy']);
    });
});
