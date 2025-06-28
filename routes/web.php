<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IncidentController;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Http\Controllers\ExportJobOrderController;
use App\Http\Controllers\JobOrderController;
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
        Route::get('waste-managements', fn () => dd('wm'))->name('waste_management');
        Route::get('it-services', fn () => dd('it'))->name('it_service');
        Route::get('others', fn () => dd('os'))->name('others');
    });

  Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('report', [IncidentController::class, 'showReport'])->name('report');
        Route::get('/', [IncidentController::class, 'index'])->name('index');
        Route::post('/', [IncidentController::class, 'store'])->name('store');
        Route::patch('{incident}/read', [IncidentController::class, 'markAsRead'])->name('read');
        Route::post('/archive', [IncidentController::class, 'archive'])->name('archive');
    });

    
    Route::prefix('data')->group(function () {
        Route::get('employees/dropdown', [EmployeeController::class, 'dropdown'])->name('employees.dropdown');
                Route::get('/job-orders/dropdown', [JobOrderController::class, 'dropdownOptions']);

    });});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
