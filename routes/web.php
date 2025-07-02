<?php

use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\EmployeeRatingController;
use App\Http\Controllers\ITServicesController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IncidentController;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Http\Controllers\ExportJobOrderController;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Home (Default route with Inertia)
    Route::inertia('/', 'Home')->name('home');

    /*
    |--------------------------------------------------------------------------
    | Job Orders
    |--------------------------------------------------------------------------
    */
    Route::prefix('job-orders')->name('job_order.')->group(function () {
        Route::get('/', [JobOrderController::class, 'index'])->name('index');
        Route::get('create', [JobOrderController::class, 'create'])->name('create');
        Route::post('/', [JobOrderController::class, 'store'])->name('store');

        // Serviceable Types
        Route::delete('{jobOrder?}', [JobOrderController::class, 'destroy'])->name('destroy');
        Route::get('export', ExportJobOrderController::class)->name('export');
        Route::get('waste-managements', fn() => dd('wm'))->name('waste_management');
        Route::get('others', fn() => dd('os'))->name('others');

        // IT Services nested under job orders
        Route::get('it-services', [ITServicesController::class, 'index'])->name('it_service');
        Route::post('it-services/archive', [ITServicesController::class, 'archive'])->name('it_service.archive');
    });

    /*
    |--------------------------------------------------------------------------
    | IT Services (Main Create & Store)
    |--------------------------------------------------------------------------
    */

    Route::prefix('it-services')->name('it-services.')->group(function () {
        Route::get('/create', [ITServicesController::class, 'create'])->name('create');
        Route::post('/', [ITServicesController::class, 'store'])->name('store');
    });

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
    //Employee Routes
    Route::prefix('ratings')->name('employee.ratings.')->group(function () {
        Route::get('/', [EmployeeRatingController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeRatingController::class, 'create'])->name('create');
        Route::post('/store', [EmployeeRatingController::class, 'store'])->name('store');
        Route::get('/view', [EmployeeRatingController::class, 'view'])->name('view');
        Route::post('/archive', [EmployeeRatingController::class, 'archive'])->name('archive');
    });

    //Consultant & Admin Routes also
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
});

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

/*
    |--------------------------------------------------------------------------
    | Roles and Permissions
    |--------------------------------------------------------------------------
    */

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

/*
|--------------------------------------------------------------------------
| Additional Route Files
|--------------------------------------------------------------------------
*/
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
