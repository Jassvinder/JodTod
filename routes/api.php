<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\GroupExpenseController;
use App\Http\Controllers\Api\V1\IncomeController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OtpController;
use App\Http\Controllers\Api\V1\SettlementController;
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

    // Public routes (no auth required)
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/otp/send', [OtpController::class, 'send']);
    Route::post('/otp/verify', [OtpController::class, 'verify']);

    // Protected routes (Sanctum token auth + banned check)
    Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckBanned::class])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/email/verification-notification', [AuthController::class, 'resendVerification']);

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit']);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::delete('/profile', [ProfileController::class, 'destroy']);

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
        Route::put('/incomes/{income}', [IncomeController::class, 'update']);
        Route::delete('/incomes/{income}', [IncomeController::class, 'destroy']);
        Route::get('/income-suggestions', [IncomeController::class, 'suggestions']);

        // Contacts
        Route::get('/contacts', [ContactController::class, 'index']);
        Route::get('/contacts/search', [ContactController::class, 'search']);
        Route::post('/contacts', [ContactController::class, 'store']);
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);

        // Categories
        Route::get('/categories', [CategoryController::class, 'index']);

        // Todos
        Route::get('/todos', [TodoController::class, 'index']);
        Route::post('/todos', [TodoController::class, 'store']);
        Route::put('/todos/{todo}', [TodoController::class, 'update']);
        Route::delete('/todos/{todo}', [TodoController::class, 'destroy']);
        Route::patch('/todos/{todo}/toggle', [TodoController::class, 'toggle']);

        // Todo Categories
        Route::get('/todo-categories', [TodoCategoryController::class, 'index']);
        Route::post('/todo-categories', [TodoCategoryController::class, 'store']);
        Route::put('/todo-categories/{todoCategory}', [TodoCategoryController::class, 'update']);
        Route::delete('/todo-categories/{todoCategory}', [TodoCategoryController::class, 'destroy']);

        // Groups
        Route::get('/groups', [GroupController::class, 'index']);
        Route::post('/groups', [GroupController::class, 'store']);
        Route::get('/groups/{group}', [GroupController::class, 'show']);
        Route::put('/groups/{group}', [GroupController::class, 'update']);
        Route::delete('/groups/{group}', [GroupController::class, 'destroy']);
        Route::post('/groups/join', [GroupController::class, 'join']);
        Route::post('/groups/{group}/leave', [GroupController::class, 'leave']);
        Route::post('/groups/{group}/add-member', [GroupController::class, 'addMember']);
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
    });
});
