<?php

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CancelledJobOrderController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\EmployeeRatingController;
use App\Http\Controllers\ExportActivityLogController;
use App\Http\Controllers\ExportFrontlinerRankingsController;
use App\Http\Controllers\ExportJobOrderController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ITServicesController;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\JobOrderCorrectionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SafetyInspectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WasteManagementController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // Home (Default route with Inertia)
    Route::inertia('/', 'Home')->name('home');

    /*
    |--------------------------------------------------------------------------
    | Job Orders
    |--------------------------------------------------------------------------
    */
    Route::prefix('job-orders')->name('job_order.')->group(function () {
        Route::get('/', [JobOrderController::class, 'index'])
            ->name('index')
            ->middleware([
                'role_or_permission:'.
                    UserRole::Frontliner->value.'|'.
                    UserPermission::ViewAnyJobOrder->value,
            ]);
        Route::get('create', [JobOrderController::class, 'create'])
            ->name('create')
            ->can('create', 'App\\Models\JobOrder');
        Route::patch('{jobOrder}', [JobOrderController::class, 'update'])->name('update');
        Route::delete('{jobOrder?}', [JobOrderController::class, 'destroy'])->name('destroy');
        Route::get('export', ExportJobOrderController::class)->name('export');
        Route::get('export-frontliner', ExportFrontlinerRankingsController::class)->name('export.frontliner_rankings');

        Route::prefix('waste-managements')->name('waste_management.')->group(function () {
            Route::get('/', [WasteManagementController::class, 'index'])->name('index');
            Route::get('{ticket}/edit', [WasteManagementController::class, 'edit'])
                ->name('edit')
                ->can('view', 'ticket');
            Route::post('/', [WasteManagementController::class, 'store'])->name('store');
            Route::patch('{form4}', [WasteManagementController::class, 'update'])->name('update');

            Route::patch('{checklist}/safety-inspection', [SafetyInspectionController::class, 'update'])
                ->name('safety_inspection.update');
        });

        Route::prefix('it-services')->name('it_service.')->group(function () {
            Route::get('/', [ITServicesController::class, 'index'])->name('index');
            Route::get('/create', [ITServicesController::class, 'create'])->name('create');
            Route::post('/', [ITServicesController::class, 'store'])->name('store');

            Route::get('{jobOrder}/edit', fn () => dd('it eit'))->name('edit');
            Route::post('/archive', [ITServicesController::class, 'archive'])->name('archive');

        });

        Route::prefix('others')->name('other.')->group(function () {
            Route::get('/', fn () => dd('os'))->name('index');
            Route::get('{jobOrder}/edit', fn () => dd('it eit'))->name('edit');
        });

        Route::prefix('cancels')->name('cancel.')->group(function () {
            Route::post('{jobOrder}', [CancelledJobOrderController::class, 'create'])->name('create');
        });

        Route::prefix('corrections')->name('correction.')->group(function () {
            Route::post('{ticket}/', [JobOrderCorrectionController::class, 'store'])->name('store');
        });
    });

    Route::middleware(['can:viewActivityLogs'])->prefix('activities')->name('activity.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('export', ExportActivityLogController::class)->name('export');
    });

    Route::get('reports', [ReportController::class, 'index'])->name('report.index');

    /*
    |--------------------------------------------------------------------------
    | Employee Profiles
    |--------------------------------------------------------------------------
    */

    Route::prefix('profile')->name('employees.profile.')->group(function () {
        Route::get('/{id}', [EmployeeProfileController::class, 'show'])->name('show');
        Route::patch('/{id}', [EmployeeProfileController::class, 'update'])->name('update');
    });

    /*
    |--------------------------------------------------------------------------
    | Employee Ratings
    |--------------------------------------------------------------------------
    */
    // Employee Routes
    Route::prefix('ratings')->name('employee.ratings.')->group(function () {
        Route::get('/', [EmployeeRatingController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeRatingController::class, 'create'])->name('create');
        Route::post('/store', [EmployeeRatingController::class, 'store'])->name('store');
        Route::get('/view', [EmployeeRatingController::class, 'view'])->name('view');
        Route::post('/archive', [EmployeeRatingController::class, 'archive'])->name('archive');
    });

    // Consultant & Admin Routes also
    Route::get('ratings/table', [EmployeeRatingController::class, 'ratingsTable'])->name('table');
    Route::get('/employee-ratings/{employee}/history', [EmployeeRatingController::class, 'ratingHistory'])->name('employee_ratings.history');
    Route::get('/employee-ratings/{employee}/history-page', [EmployeeRatingController::class, 'historyPage'])->name('employee.ratings.history.page');

    /*
    |--------------------------------------------------------------------------
    | Archive
    |--------------------------------------------------------------------------
    */

    Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::post('/archives/restore', [ArchiveController::class, 'restore'])->name('archives.restore');
    Route::post('/archives/force-delete', [ArchiveController::class, 'forceDelete'])->name('archives.force-delete');

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */

    Route::prefix('users')->group(function () {

        Route::get('/', [UserController::class, 'index']);

        Route::get('/{user}/settings', [UserController::class, 'settings'])
            ->name('users.settings');

        Route::patch('/{user}', [UserController::class, 'update'])
            ->name('users.update');
        Route::patch('/{user}/role', [UserController::class, 'updateRole'])
            ->name('users.role.update');
        Route::delete('/{user}/deactivate', [UserController::class, 'deactivate'])
            ->name('users.deactivate');
        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });
    Route::get('/roles-permissions', function () {
        return inertia('roles-and-permissions/RolesPermissions');
    })->name('roles-permissions');
    Route::post('/employees-with-account', [EmployeeController::class, 'storeWithAccount']);
    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('report', [IncidentController::class, 'showReport'])->name('report');
        Route::get('/', [IncidentController::class, 'index'])->name('index');
        Route::post('/', [IncidentController::class, 'store'])->name('store');
        Route::patch('{incident}/read', [IncidentController::class, 'markAsRead'])->name('read');
        Route::post('/archive', [IncidentController::class, 'archive'])->name('archive');
        Route::patch('/{incident}/verify', [IncidentController::class, 'verify'])
            ->middleware(['can:verify,incident']);

    });

    Route::prefix('data')->group(function () {
        Route::get('employees/dropdown', [EmployeeController::class, 'dropdown'])->name('employees.dropdown');
        Route::get('/job-orders/dropdown', [JobOrderController::class, 'dropdownOptions']);
        Route::get('/users', [UserController::class, 'getUsersData']);
        Route::get('/positions', [\App\Http\Controllers\PositionController::class, 'index']);
    });
});

Route::get('test', function () {
    $dispatchers = User::role(UserRole::Dispatcher)->get()->pluck('email')->toArray();
    $teamLeaders = User::role(UserRole::TeamLeader)->get()->pluck('email')->toArray();
    $head        = User::role(UserRole::HeadFrontliner)->first()->email;
    $itAdmins    = User::role(UserRole::ITAdmin)->get()->pluck('email')->toArray();
    $consultants = User::role(UserRole::Consultant)->get()->pluck('email')->toArray();
    dd(
        [
            'dispatchers'  => $dispatchers,
            'team leaders' => $teamLeaders,
            'head'         => $head,
            'it admins'    => $itAdmins,
            'consultants'  => $consultants,
            User::first()->permissions()
        ],
    );
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
