<?php

use App\Http\Controllers\ArchivedJobOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('archives')->name('archive.')->group(function () {
    Route::prefix('job-orders')->name('job_order.')->group(function() {
        Route::get('/', [ArchivedJobOrderController::class, 'index'])
            ->name('index');

        Route::patch('{jobOrder}', [ArchivedJobOrderController::class, 'update'])
            ->name('update')
            ->withTrashed();
    });
});
