<?php

use App\Http\Controllers\ExportJobOrderController;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\WasteManagementController;
use Illuminate\Support\Facades\Route;

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
            Route::get('{jobOrder}/edit', [WasteManagementController::class, 'edit'])->name('edit');
            Route::post('/', [WasteManagementController::class, 'store'])->name('store');
        });

        Route::prefix('it-services')->name('it_service.')->group(function () {
            Route::get('/', fn () => dd('it'))->name('index');
            Route::get('{jobOrder}/edit', fn () => dd('it eit'))->name('edit');
        });

        Route::prefix('others')->name('other.')->group(function () {
            Route::get('/', fn () => dd('os'))->name('index');
        });
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
