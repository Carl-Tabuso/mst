<?php

use App\Http\Controllers\ExportJobOrderCorrectionController;
use App\Http\Controllers\JobOrderCorrectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('job-orders/corrections')->name('job_order.correction.')->group(function () {
        Route::get('/', [JobOrderCorrectionController::class, 'index'])
            ->name('index')
            ->can('viewAny', 'App\\Models\JobOrderCorrection');

        Route::get('export', ExportJobOrderCorrectionController::class)
            ->name('export')
            ->can('viewAny', 'App\\Models\JobOrderCorrection');

        Route::delete('bulk-destroy', [JobOrderCorrectionController::class, 'bulkDestroy'])
            ->name('bulk_destroy')
            ->can('delete', 'App\\Models\JobOrderCorrection');

        Route::get('{correction}', [JobOrderCorrectionController::class, 'show'])
            ->name('show')
            ->can('view', 'correction');

        Route::post('{ticket}', [JobOrderCorrectionController::class, 'store'])
            ->name('store');

        Route::patch('{correction}', [JobOrderCorrectionController::class, 'update'])
            ->name('update');

        Route::delete('{correction}', [JobOrderCorrectionController::class, 'destroy'])
            ->name('destroy')
            ->can('delete', 'App\\Models\JobOrderCorrection');
    });
});
