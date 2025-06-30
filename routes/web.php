<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IncidentController;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Http\Controllers\ExportJobOrderController;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WasteManagementController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->group(function () {
    Route::inertia('/', 'Home')->name('home');

    Route::prefix('job-orders')->name('job_order.')->group(function () {
        Route::get('/', [JobOrderController::class, 'index'])->name('index');
        Route::get('create', [JobOrderController::class, 'create'])->name('create');
        Route::post('/', [JobOrderController::class, 'store'])->name('store');
        Route::delete('{jobOrder?}', [JobOrderController::class, 'destroy'])->name('destroy');
        Route::get('export', ExportJobOrderController::class)->name('export');

        Route::prefix('waste-managements')->name('waste_management.')->group(function () {
            Route::get('/', [WasteManagementController::class, 'index'])->name('index');
            Route::get('{form4}/edit', [WasteManagementController::class, 'edit'])->name('edit');
            Route::post('/', [WasteManagementController::class, 'store'])->name('store');
            Route::patch('{form4}', [WasteManagementController::class, 'update'])->name('update');
        });

        Route::prefix('it-services')->name('it_service.')->group(function () {
            Route::get('/', fn () => dd('it'))->name('index');
            Route::get('{jobOrder}/edit', fn () => dd('it eit'))->name('edit');
        });

        Route::prefix('others')->name('other.')->group(function () {
            Route::get('/', fn () => dd('os'))->name('index');
        });
    });

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



    });});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
