<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ApiTaskController;
use App\Http\Controllers\Api\ApiReportController;
use App\Http\Controllers\Api\ApiStaffController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

// ── Public ───────────────────────────────────────────────────────────────────
Route::post('/auth/login', [AuthController::class, 'login']);

// ── Inbound Webhook (uses X-Webhook-Token, not Sanctum) ──────────────────────
Route::post('/webhook/inbound', [WebhookController::class, 'inbound']);

// ── Authenticated (Sanctum Bearer token) ─────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard/my',    [DashboardController::class, 'myDashboard']);
    Route::get('/dashboard/admin', [DashboardController::class, 'adminDashboard']);

    // Tasks
    Route::get('/tasks',               [ApiTaskController::class, 'index']);
    Route::post('/tasks',              [ApiTaskController::class, 'store']);
    Route::get('/tasks/{id}',          [ApiTaskController::class, 'show']);
    Route::put('/tasks/{id}',          [ApiTaskController::class, 'update']);
    Route::delete('/tasks/{id}',       [ApiTaskController::class, 'destroy']);
    Route::post('/tasks/{id}/comment', [ApiTaskController::class, 'addComment']);
    Route::patch('/tasks/{id}/status', [ApiTaskController::class, 'statusUpdate']);

    // Reports
    Route::post('/reports',         [ApiReportController::class, 'submit']);
    Route::get('/reports/summary',  [ApiReportController::class, 'summary']);
    Route::get('/reports/today',    [ApiReportController::class, 'today']);
    Route::get('/reports/missing',  [ApiReportController::class, 'missing']);

    // Staff / Team
    Route::get('/staff',                [ApiStaffController::class, 'staffList']);
    Route::get('/team',                 [ApiStaffController::class, 'teamList']);
    Route::post('/team',                [ApiStaffController::class, 'teamAdd']);
    Route::put('/team/{id}',            [ApiStaffController::class, 'teamUpdate']);
    Route::patch('/team/{id}/toggle',   [ApiStaffController::class, 'teamToggle']);
});
