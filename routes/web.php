<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReportCalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TouchPointController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Crm\DashboardController as CrmDashboardController;
use App\Http\Controllers\Crm\CustomerController as CrmCustomerController;
use App\Http\Controllers\Crm\CustomerDetailController as CrmCustomerDetailController;
use App\Http\Controllers\Crm\WatchController as CrmWatchController;
use App\Http\Controllers\Crm\SalesTeamController as CrmSalesTeamController;
use App\Http\Controllers\Crm\SettingsController as CrmSettingsController;
use App\Http\Controllers\Hr\RegisterController;
use App\Http\Controllers\Hr\DashboardController as HrDashboardController;
use App\Http\Controllers\Hr\ApplicationController as HrApplicationController;
use App\Http\Controllers\Hr\JobOpeningController as HrJobOpeningController;
use App\Http\Controllers\Hr\EmployeeController as HrEmployeeController;
use App\Http\Controllers\Hr\AttendanceController as HrAttendanceController;
use App\Http\Controllers\Hr\LeaveController as HrLeaveController;
use App\Http\Controllers\Hr\PayrollController as HrPayrollController;
use App\Http\Controllers\Hr\SettingsController as HrSettingsController;
use Illuminate\Support\Facades\Route;

// Guest routes (unauthenticated only)
Route::middleware('guest:staff')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Public CRM watch page (customer-facing — no auth; identity is the link token)
Route::prefix('crm')->name('crm.')->group(function () {
    Route::get('/watch',        [CrmWatchController::class, 'show'])->name('watch');
    Route::post('/watch/track', [CrmWatchController::class, 'track'])->name('watch.track');
});

// HR section (public — job application form + submission)
Route::prefix('hr')->name('hr.')->group(function () {
    Route::get('/register',        [RegisterController::class, 'index'])->name('register');
    Route::post('/register',       [RegisterController::class, 'store'])->name('register.store');
    Route::get('/register/finish', [RegisterController::class, 'finish'])->name('register.finish');
});


// Authenticated routes
Route::middleware('auth:staff')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // HR Management (admin/HR) — controllers in App\Http\Controllers\Hr
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('/dashboard',                 [HrDashboardController::class, 'index'])->name('dashboard');

        Route::get('/applications',              [HrApplicationController::class, 'index'])->name('applications');
        Route::get('/applications/data',         [HrApplicationController::class, 'data'])->name('applications.data');
        Route::get('/applications/{id}',         [HrApplicationController::class, 'show'])->whereNumber('id')->name('applications.show');
        Route::post('/applications/{id}/status', [HrApplicationController::class, 'updateStatus'])->whereNumber('id')->name('applications.status');
        Route::delete('/applications/{id}',      [HrApplicationController::class, 'destroy'])->whereNumber('id')->name('applications.destroy');

        Route::get('/employees',                 [HrEmployeeController::class, 'index'])->name('employees');
        Route::get('/employees/data',            [HrEmployeeController::class, 'data'])->name('employees.data');
        Route::get('/employees/create',          [HrEmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees',                [HrEmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{id}',            [HrEmployeeController::class, 'show'])->whereNumber('id')->name('employees.show');
        Route::get('/employees/{id}/attendance', [HrEmployeeController::class, 'attendance'])->whereNumber('id')->name('employees.attendance');
        Route::post('/employees/{id}/salary',    [HrEmployeeController::class, 'updateSalary'])->whereNumber('id')->name('employees.salary');
        Route::post('/employees/{id}/bank',      [HrEmployeeController::class, 'updateBank'])->whereNumber('id')->name('employees.bank');
        Route::post('/employees/{id}/documents', [HrEmployeeController::class, 'uploadDocument'])->whereNumber('id')->name('employees.documents.store');
        Route::delete('/employees/{id}/documents/{doc}', [HrEmployeeController::class, 'deleteDocument'])->whereNumber('id')->whereNumber('doc')->name('employees.documents.destroy');
        Route::get('/employees/{id}/edit',       [HrEmployeeController::class, 'edit'])->whereNumber('id')->name('employees.edit');
        Route::put('/employees/{id}',            [HrEmployeeController::class, 'update'])->whereNumber('id')->name('employees.update');

        // Attendance
        Route::get('/attendance',                [HrAttendanceController::class, 'index'])->name('attendance');
        Route::get('/attendance/data',           [HrAttendanceController::class, 'data'])->name('attendance.data');
        Route::get('/attendance/employees',      [HrAttendanceController::class, 'employeesByDate'])->name('attendance.employees');
        Route::get('/attendance/export',         [HrAttendanceController::class, 'export'])->name('attendance.export');
        Route::post('/attendance',               [HrAttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/{id}/edit',      [HrAttendanceController::class, 'edit'])->whereNumber('id')->name('attendance.edit');
        Route::delete('/attendance/{id}',        [HrAttendanceController::class, 'destroy'])->whereNumber('id')->name('attendance.destroy');

        // Leave Requests
        Route::get('/leave-requests',            [HrLeaveController::class, 'index'])->name('leave-requests');
        Route::get('/leave-requests/data',       [HrLeaveController::class, 'data'])->name('leave-requests.data');
        Route::get('/leave-requests/counts',     [HrLeaveController::class, 'counts'])->name('leave-requests.counts');
        Route::post('/leave-requests',           [HrLeaveController::class, 'store'])->name('leave-requests.store');
        Route::post('/leave-requests/{id}/status', [HrLeaveController::class, 'updateStatus'])->whereNumber('id')->name('leave-requests.status');
        Route::delete('/leave-requests/{id}',    [HrLeaveController::class, 'destroy'])->whereNumber('id')->name('leave-requests.destroy');

        // Payroll
        Route::get('/payroll',                   [HrPayrollController::class, 'index'])->name('payroll');
        Route::get('/payroll/export',            [HrPayrollController::class, 'export'])->name('payroll.export');
        Route::post('/payroll/set-salary',       [HrPayrollController::class, 'setBaseSalary'])->name('payroll.set-salary');
        Route::post('/payroll/update-salary',    [HrPayrollController::class, 'updateBaseSalary'])->name('payroll.update-salary');
        Route::post('/payroll/process',          [HrPayrollController::class, 'process'])->name('payroll.process');

        Route::get('/job-openings',              [HrJobOpeningController::class, 'index'])->name('job-openings');
        Route::get('/job-openings/data',         [HrJobOpeningController::class, 'data'])->name('job-openings.data');
        Route::get('/job-openings/create',       [HrJobOpeningController::class, 'create'])->name('job-openings.create');
        Route::post('/job-openings',             [HrJobOpeningController::class, 'store'])->name('job-openings.store');
        Route::get('/job-openings/{id}/edit',    [HrJobOpeningController::class, 'edit'])->whereNumber('id')->name('job-openings.edit');
        Route::put('/job-openings/{id}',         [HrJobOpeningController::class, 'update'])->whereNumber('id')->name('job-openings.update');
        Route::delete('/job-openings/{id}',      [HrJobOpeningController::class, 'destroy'])->whereNumber('id')->name('job-openings.destroy');
        Route::post('/job-openings/{id}/toggle', [HrJobOpeningController::class, 'toggle'])->whereNumber('id')->name('job-openings.toggle');

        // Settings (vertical-tab hub — Job Category tab implemented)
        Route::get('/settings',                       [HrSettingsController::class, 'index'])->name('settings');
        Route::get('/settings/job-categories/data',   [HrSettingsController::class, 'jobCategoryData'])->name('settings.job-categories.data');
        Route::post('/settings/job-categories',       [HrSettingsController::class, 'jobCategoryStore'])->name('settings.job-categories.store');
        Route::put('/settings/job-categories/{id}',   [HrSettingsController::class, 'jobCategoryUpdate'])->whereNumber('id')->name('settings.job-categories.update');
        Route::post('/settings/job-categories/{id}/toggle', [HrSettingsController::class, 'jobCategoryToggle'])->whereNumber('id')->name('settings.job-categories.toggle');
        Route::delete('/settings/job-categories/{id}', [HrSettingsController::class, 'jobCategoryDestroy'])->whereNumber('id')->name('settings.job-categories.destroy');
        Route::get('/settings/qualifications/data',   [HrSettingsController::class, 'qualificationData'])->name('settings.qualifications.data');
        Route::post('/settings/qualifications',       [HrSettingsController::class, 'qualificationStore'])->name('settings.qualifications.store');
        Route::put('/settings/qualifications/{id}',   [HrSettingsController::class, 'qualificationUpdate'])->whereNumber('id')->name('settings.qualifications.update');
        Route::delete('/settings/qualifications/{id}', [HrSettingsController::class, 'qualificationDestroy'])->whereNumber('id')->name('settings.qualifications.destroy');
        Route::get('/settings/departments/data',      [HrSettingsController::class, 'departmentData'])->name('settings.departments.data');
        Route::post('/settings/departments',          [HrSettingsController::class, 'departmentStore'])->name('settings.departments.store');
        Route::put('/settings/departments/{id}',      [HrSettingsController::class, 'departmentUpdate'])->whereNumber('id')->name('settings.departments.update');
        Route::delete('/settings/departments/{id}',   [HrSettingsController::class, 'departmentDestroy'])->whereNumber('id')->name('settings.departments.destroy');
        Route::get('/settings/designations/data',     [HrSettingsController::class, 'designationData'])->name('settings.designations.data');
        Route::post('/settings/designations',         [HrSettingsController::class, 'designationStore'])->name('settings.designations.store');
        Route::put('/settings/designations/{id}',     [HrSettingsController::class, 'designationUpdate'])->whereNumber('id')->name('settings.designations.update');
        Route::delete('/settings/designations/{id}',  [HrSettingsController::class, 'designationDestroy'])->whereNumber('id')->name('settings.designations.destroy');
        Route::get('/settings/leave-settings/types',  [HrSettingsController::class, 'leaveSettingTypes'])->name('settings.leave-settings.types');
        Route::get('/settings/leave-settings/data',   [HrSettingsController::class, 'leaveSettingData'])->name('settings.leave-settings.data');
        Route::post('/settings/leave-settings',       [HrSettingsController::class, 'leaveSettingStore'])->name('settings.leave-settings.store');
        Route::put('/settings/leave-settings/{id}',   [HrSettingsController::class, 'leaveSettingUpdate'])->whereNumber('id')->name('settings.leave-settings.update');
        Route::delete('/settings/leave-settings/{id}', [HrSettingsController::class, 'leaveSettingDestroy'])->whereNumber('id')->name('settings.leave-settings.destroy');
        Route::get('/settings/allowances/data',       [HrSettingsController::class, 'allowanceData'])->name('settings.allowances.data');
        Route::post('/settings/allowances',           [HrSettingsController::class, 'allowanceStore'])->name('settings.allowances.store');
        Route::put('/settings/allowances/{id}',       [HrSettingsController::class, 'allowanceUpdate'])->whereNumber('id')->name('settings.allowances.update');
        Route::delete('/settings/allowances/{id}',    [HrSettingsController::class, 'allowanceDestroy'])->whereNumber('id')->name('settings.allowances.destroy');
        Route::post('/settings/notifications/toggle', [HrSettingsController::class, 'notificationToggle'])->name('settings.notifications.toggle');
    });

    // Clients
    Route::get('/clients',            [ClientController::class, 'index'])->name('clients');
    Route::post('/clients',           [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{client}',   [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}',[ClientController::class, 'destroy'])->name('clients.destroy');

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

    // Assets — page path is /assets-list to avoid colliding with the physical
    // public/assets/ directory (which the web server would serve instead).
    Route::get('/assets-list',               [AssetController::class, 'index'])->name('assets');
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



    // Getlead CRM module (ported from crmdemo) — separate controllers per feature
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::get('/dashboard', [CrmDashboardController::class, 'index'])->name('dashboard');

        // Customers
        Route::get('/customers',                 [CrmCustomerController::class, 'index'])->name('customers');
        Route::get('/customers/create',          [CrmCustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers',                [CrmCustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}',      [CrmCustomerDetailController::class, 'show'])->name('customers.show');
        Route::delete('/customers/{customer}',   [CrmCustomerController::class, 'destroy'])->name('customers.destroy');

        // Sales Team (writes to the staff table with role = sales_rep)
        Route::get('/sales-team',                [CrmSalesTeamController::class, 'index'])->name('sales-team');
        Route::get('/sales-team/create',         [CrmSalesTeamController::class, 'create'])->name('sales-team.create');
        Route::post('/sales-team',               [CrmSalesTeamController::class, 'store'])->name('sales-team.store');
        Route::get('/sales-team/{member}/edit',  [CrmSalesTeamController::class, 'edit'])->name('sales-team.edit');
        Route::put('/sales-team/{member}',       [CrmSalesTeamController::class, 'update'])->name('sales-team.update');
        Route::delete('/sales-team/{member}',    [CrmSalesTeamController::class, 'destroy'])->name('sales-team.destroy');

        // Settings (landing/branding + Telegram) — stored in the settings table
        Route::get('/settings',           [CrmSettingsController::class, 'index'])->name('settings');
        Route::post('/settings/landing',  [CrmSettingsController::class, 'updateLanding'])->name('settings.landing');
        Route::post('/settings/telegram', [CrmSettingsController::class, 'updateTelegram'])->name('settings.telegram');
        Route::post('/settings/test',     [CrmSettingsController::class, 'sendTest'])->name('settings.test');
    });

});

// Redirect root to login
Route::get('/', fn () => redirect()->route('login'));
