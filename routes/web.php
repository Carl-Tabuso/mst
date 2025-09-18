<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\CancelledJobOrderController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\EmployeeRatingController;
use App\Http\Controllers\ExportActivityLogController;
use App\Http\Controllers\ExportJobOrderController;
use App\Http\Controllers\ExportReportsController;
use App\Http\Controllers\Form5Controller;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SafetyInspectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WasteManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    /*
    |--------------------------------------------------------------------------
    | Job Orders
    |--------------------------------------------------------------------------
    */
    Route::prefix('job-orders')->name('job_order.')->group(function () {
        Route::get('/', [JobOrderController::class, 'index'])
            ->name('index')
            ->can('viewAny', 'App\\Models\\JobOrder');
        Route::get('create', [JobOrderController::class, 'create'])
            ->name('create')
            ->can('create', 'App\\Models\JobOrder');
        Route::delete('bulk-destroy', [JobOrderController::class, 'bulkDestroy'])
            ->name('bulk_destroy')
            ->can('deleteBatch', 'App\\Models\JobOrder');
        Route::get('export', ExportJobOrderController::class)
            ->name('export')
            ->can('viewAny', 'App\\Models\JobOrder');
        Route::patch('{jobOrder}', [JobOrderController::class, 'update'])
            ->name('update')
            ->can('update', 'jobOrder');
        Route::delete('{jobOrder}', [JobOrderController::class, 'destroy'])
            ->name('destroy')
            ->can('update', 'jobOrder');
        Route::prefix('other-services')->name('other_services.')->group(function () {
            Route::get('/', [Form5Controller::class, 'index'])->name('index');
            Route::get('{ticket}/edit', [Form5Controller::class, 'edit'])
                ->name('edit')
                ->can('view', 'ticket');
            Route::post('/', [Form5Controller::class, 'store'])->name('store');
            Route::patch('{form5}', [Form5Controller::class, 'update'])->name('update');

        });
        Route::prefix('waste-managements')->name('waste_management.')->group(function () {
            Route::get('/', [WasteManagementController::class, 'index'])
                ->name('index');

            Route::get('{ticket}/edit', [WasteManagementController::class, 'edit'])
                ->name('edit')
                ->can('view', 'ticket');

            Route::post('/', [WasteManagementController::class, 'store'])
                ->name('store');

            Route::patch('{form4}', [WasteManagementController::class, 'update'])
                ->name('update');

            Route::patch('{checklist}/safety-inspection', [SafetyInspectionController::class, 'update'])
                ->name('safety_inspection.update');

            Route::prefix('appraisals')->name('appraisal.')->group(function () {
                Route::post('{form4}', [AppraisalController::class, 'store'])
                    ->name('store');

                Route::patch('{form4}', [AppraisalController::class, 'update'])
                    ->name('update');
            });
        });

        Route::prefix('cancels')->name('cancel.')->group(function () {
            Route::post('{jobOrder}', [CancelledJobOrderController::class, 'create'])->name('create');
        });
    });

    Route::prefix('activities')->name('activity.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('export', ExportActivityLogController::class)->name('export');
    });

    Route::middleware(['can:viewReports'])->prefix('reports')->name('report.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('export', ExportReportsController::class)->name('export');
    });

    /*
    |--------------------------------------------------------------------------
    | Employee Profiles
    |--------------------------------------------------------------------------
    */

    Route::prefix('profile')->name('employees.profile.')->group(function () {
        Route::get('/{id}', [EmployeeProfileController::class, 'show'])->name('show');
        Route::patch('/{id}/update', [EmployeeProfileController::class, 'update'])->name('update');
        Route::get('/{id}/performance-data', [EmployeeProfileController::class, 'getPerformanceData'])->name('performance-data');
        Route::get('/{id}/avatar', [EmployeeProfileController::class, 'avatar'])->name('avatar')->middleware('auth');
    });

    /*
    |--------------------------------------------------------------------------
    | Employee Ratings
    |--------------------------------------------------------------------------
    */

    // Employee Routes
    Route::prefix('ratings')->name('employee.ratings.')->group(function () {
        Route::get('/', [EmployeeRatingController::class, 'index'])
            ->name('index')
            ->can('viewAny', 'App\\Models\\EmployeeRating');

        Route::get('/create', [EmployeeRatingController::class, 'create'])
            ->name('create')
            ->can('create', 'App\\Models\\EmployeeRating');

        Route::post('/store', [EmployeeRatingController::class, 'store'])
            ->name('store')
            ->can('create', 'App\\Models\\EmployeeRating');

        Route::get('/view', [EmployeeRatingController::class, 'view'])
            ->name('view')
            ->can('viewAny', 'App\\Models\\EmployeeRating');
    });

    // Consultant & Admin Routes
    Route::get('ratings/table', [EmployeeRatingController::class, 'ratingsTable'])
        ->name('table')
        ->can('viewAny', 'App\\Models\\EmployeeRating');

    Route::get('/ratings/table/export', [EmployeeRatingController::class, 'export'])
        ->name('employee.ratings.export')
        ->can('export', 'App\\Models\\EmployeeRating');

    Route::get('/employee-ratings/{employee}/history', [EmployeeRatingController::class, 'ratingHistory'])
        ->name('employee_ratings.history')
        ->can('viewAny', 'App\\Models\\EmployeeRating');

    Route::get('/employee-ratings/{employee}/history-page', [EmployeeRatingController::class, 'historyPage'])
        ->name('employee.ratings.history.page')
        ->can('viewAny', 'App\\Models\\EmployeeRating');

    Route::get('/employee-ratings/{employeeId}/history-page/export', [EmployeeRatingController::class, 'exportHistory'])
        ->name('employee.ratings.history.export')
        ->can('export', 'App\\Models\\EmployeeRating');

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/{user}/settings', [UserController::class, 'settings'])
            ->name('users.settings')->withTrashed();
        Route::post('/store', [UserController::class, 'store'])->name('users.store');
        Route::patch('/{user}', [UserController::class, 'update'])
            ->name('users.update');
        Route::patch('/{user}/role', [UserController::class, 'updateRole'])
            ->name('users.role.update');
        Route::delete('/{user}/deactivate', [UserController::class, 'deactivate'])
            ->name('users.deactivate');
        Route::post('/{user}/restore', [UserController::class, 'restore'])
            ->name('users.restore')->withTrashed();
        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });
    Route::get('/roles-permissions', function () {
        return inertia('roles-and-permissions/RolesPermissions');
    })->name('roles-permissions');

    Route::post('/employees-with-account', [EmployeeController::class, 'storeWithAccount']);
    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/', action: [IncidentController::class, 'index'])->name('index');
        Route::put('/{incident}', [IncidentController::class, 'update'])->name('update');
        Route::post('/', [IncidentController::class, 'store'])->name('store');
        Route::patch('{incident}/read', [IncidentController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/archive', [IncidentController::class, 'archive'])->name('archive');
        Route::patch('/{incident}/verify', [IncidentController::class, 'verify'])->name('verify')->middleware(['can:verify,incident']);
        Route::put('/incidents/{incident}/mark-no-incident', [IncidentController::class, 'markNoIncident'])->name('markNoIncident');
        Route::post('/create-secondary/{haulingId}', [IncidentController::class, 'createSecondary'])->name('createSecondary');

    });

    Route::resource('employee-management', EmployeeController::class)
        ->parameters(['employee-management' => 'employee']);
    Route::post('employee-management/{employee}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');

    Route::prefix('data')->group(function () {
        Route::get('employees/dropdown', [EmployeeController::class, 'dropdown'])->name('employees.dropdown');
        Route::get('/job-orders/dropdown', [JobOrderController::class, 'dropdownOptions']);
        Route::get('/users', [UserController::class, 'getUsersData']);
        Route::get('/positions', [\App\Http\Controllers\PositionController::class, 'index']);
    });
});

if (app()->isLocal()) {
    require __DIR__.'/sandbox.php';
}

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/corrections.php';
require __DIR__.'/trucks.php';
require __DIR__.'/itservice.php';
require __DIR__.'/archives.php';
