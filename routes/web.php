<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReportCalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TouchPointController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

// Guest routes (unauthenticated only)
Route::middleware('guest:staff')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth:staff')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Daily Report
    Route::get('/daily-report',        [DailyReportController::class, 'index'])->name('daily-report');
    Route::post('/daily-report',       [DailyReportController::class, 'store'])->name('daily-report.store');
    Route::get('/daily-report/recent', [DailyReportController::class, 'recent'])->name('daily-report.recent');

    // Report Calendar (admin only)
    Route::get('/report-calendar',          [ReportCalendarController::class, 'index'])->name('report-calendar');
    Route::get('/report-calendar/api/data', [ReportCalendarController::class, 'apiData'])->name('report-calendar.api');

    // Tasks
    Route::get('/tasks',                      [TaskController::class, 'index'])->name('tasks');
    Route::get('/my-tasks',                   [TaskController::class, 'index'])->name('my-tasks');
    Route::get('/tasks/datatable',            [TaskController::class, 'datatable'])->name('tasks.datatable');
    Route::post('/tasks',                     [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}',               [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}',               [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}',            [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/comments',     [TaskController::class, 'addComment'])->name('tasks.comment');

    // TouchPoint
    Route::get('/touchpoint',                         [TouchPointController::class, 'index'])->name('touchpoint');
    Route::get('/touchpoint/api/dashboard',           [TouchPointController::class, 'apiDashboard'])->name('touchpoint.dashboard');
    Route::get('/touchpoint/api/customers',           [TouchPointController::class, 'apiCustomers'])->name('touchpoint.customers');
    Route::post('/touchpoint/api/customer-save',      [TouchPointController::class, 'apiCustomerSave'])->name('touchpoint.customer-save');
    Route::post('/touchpoint/api/customer-delete',    [TouchPointController::class, 'apiCustomerDelete'])->name('touchpoint.customer-delete');
    Route::post('/touchpoint/api/customer-regen',     [TouchPointController::class, 'apiCustomerRegen'])->name('touchpoint.customer-regen');
    Route::get('/touchpoint/api/touchpoints',         [TouchPointController::class, 'apiTouchpoints'])->name('touchpoint.touchpoints');
    Route::post('/touchpoint/api/tp-assign',          [TouchPointController::class, 'apiTpAssign'])->name('touchpoint.tp-assign');
    Route::post('/touchpoint/api/tp-complete',        [TouchPointController::class, 'apiTpComplete'])->name('touchpoint.tp-complete');
    Route::post('/touchpoint/api/tp-bulk-assign',     [TouchPointController::class, 'apiTpBulkAssign'])->name('touchpoint.tp-bulk-assign');
    Route::post('/touchpoint/api/extend-trial',       [TouchPointController::class, 'apiExtendTrial'])->name('touchpoint.extend-trial');
    Route::post('/touchpoint/api/convert-trial',      [TouchPointController::class, 'apiConvertTrial'])->name('touchpoint.convert-trial');
    Route::post('/touchpoint/api/log-call',           [TouchPointController::class, 'apiLogCall'])->name('touchpoint.log-call');
    Route::get('/touchpoint/api/call-logs',           [TouchPointController::class, 'apiCallLogs'])->name('touchpoint.call-logs');
    Route::get('/touchpoint/api/reports',             [TouchPointController::class, 'apiReports'])->name('touchpoint.reports');

    // Assets
    Route::get('/assets',                    [AssetController::class, 'index'])->name('assets');
    Route::get('/assets/api/dashboard',      [AssetController::class, 'apiDashboard'])->name('assets.dashboard');
    Route::get('/assets/api/list',           [AssetController::class, 'apiList'])->name('assets.list');
    Route::get('/assets/api/detail/{id}',    [AssetController::class, 'apiDetail'])->name('assets.detail');
    Route::post('/assets/api/save',          [AssetController::class, 'apiSave'])->name('assets.save');
    Route::post('/assets/api/delete',        [AssetController::class, 'apiDelete'])->name('assets.delete');
    Route::post('/assets/api/repair',        [AssetController::class, 'apiRepair'])->name('assets.repair');
    Route::post('/assets/api/checkup',       [AssetController::class, 'apiCheckup'])->name('assets.checkup');
    Route::get('/assets/api/qr-codes',       [AssetController::class, 'apiQrCodes'])->name('assets.qr-codes');
    Route::get('/assets/api/qr-map-data',    [AssetController::class, 'apiQrMapData'])->name('assets.qr-map-data');
    Route::post('/assets/api/qr-map',        [AssetController::class, 'apiQrMap'])->name('assets.qr-map');
    Route::post('/assets/api/qr-unmap',      [AssetController::class, 'apiQrUnmap'])->name('assets.qr-unmap');
    Route::post('/assets/api/qr-generate',   [AssetController::class, 'apiQrGenerate'])->name('assets.qr-generate');
    Route::get('/assets/qr-lookup',          [AssetController::class, 'qrLookup'])->name('assets.qr-lookup');

    // Reports (admin only)
    Route::get('/reports',             [ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/api/summary', [ReportsController::class, 'apiSummary'])->name('reports.api.summary');

    // Team (admin only)
    Route::get('/team',             [TeamController::class, 'index'])->name('team');
    Route::get('/team/api/list',    [TeamController::class, 'apiList'])->name('team.api.list');
    Route::post('/team/api/save',   [TeamController::class, 'apiSave'])->name('team.api.save');
    Route::post('/team/api/toggle', [TeamController::class, 'apiToggle'])->name('team.api.toggle');

    // Analytics (admin only)
    Route::get('/analytics',                 [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/api/tasks',       [AnalyticsController::class, 'apiTasks'])->name('analytics.api.tasks');
    Route::get('/analytics/api/reports',     [AnalyticsController::class, 'apiReports'])->name('analytics.api.reports');
    Route::get('/analytics/api/team',        [AnalyticsController::class, 'apiTeam'])->name('analytics.api.team');
    Route::get('/analytics/api/hr',          [AnalyticsController::class, 'apiHr'])->name('analytics.api.hr');
    Route::get('/analytics/api/marketing',   [AnalyticsController::class, 'apiMarketing'])->name('analytics.api.marketing');

    // Settings (admin only)
    Route::get('/settings',                [SettingsController::class, 'index'])->name('settings');
    Route::get('/settings/api/get',        [SettingsController::class, 'apiGet'])->name('settings.api.get');
    Route::post('/settings/api/update',    [SettingsController::class, 'apiUpdate'])->name('settings.api.update');
    Route::get('/settings/api/login-history', [SettingsController::class, 'apiLoginHistory'])->name('settings.api.login-history');
    Route::get('/settings/export/data',    [SettingsController::class, 'exportData'])->name('settings.export.data');
    Route::get('/settings/export/reports', [SettingsController::class, 'exportReports'])->name('settings.export.reports');

    // Projects
    Route::get('/projects',                   [ProjectController::class, 'index'])->name('projects');
    Route::get('/projects/dashboard',         [ProjectController::class, 'dashboard'])->name('projects.dashboard');
    Route::get('/projects/list',              [ProjectController::class, 'list'])->name('projects.list');
    Route::post('/projects',                  [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}',         [ProjectController::class, 'show'])->name('projects.show');
    Route::put('/projects/{project}',         [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}',      [ProjectController::class, 'destroy'])->name('projects.destroy');
});

// Redirect root to login
Route::get('/', fn () => redirect()->route('login'));
