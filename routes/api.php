<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OAuthController;
use App\Http\Controllers\Api\PendingNotificationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SavingGoalController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google', [OAuthController::class, 'googleLogin']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('transactions', TransactionController::class);

    Route::apiResource('budgets', BudgetController::class);
    Route::apiResource('saving-goals', SavingGoalController::class);
    Route::apiResource('accounts', AccountController::class);

    Route::get('/reports/monthly', [ReportController::class, 'monthly']);
    Route::get('/reports/categories', [ReportController::class, 'categories']);
    Route::get('/reports/trend', [ReportController::class, 'trend']);
    Route::get('/reports/export', [ReportController::class, 'export']);

    Route::get('/settings', [SettingController::class, 'show']);
    Route::put('/settings', [SettingController::class, 'update']);
    Route::put('/settings/password', [SettingController::class, 'changePassword']);

    Route::get('/oauth/status', [OAuthController::class, 'status']);
    Route::delete('/oauth/google', [OAuthController::class, 'disconnect']);

    Route::post('/upload', [UploadController::class, 'upload']);

    Route::prefix('pending-notifications')->group(function () {
        Route::get('/', [PendingNotificationController::class, 'index']);
        Route::post('/', [PendingNotificationController::class, 'store']);
        Route::patch('{pending_notification}/approve', [PendingNotificationController::class, 'approve']);
        Route::delete('{pending_notification}/reject', [PendingNotificationController::class, 'reject']);
        Route::get('/count', [PendingNotificationController::class, 'count']);
    });

    Route::get('/alerts/daily', [AlertController::class, 'daily']);
});