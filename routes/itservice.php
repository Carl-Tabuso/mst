<?php

use App\Http\Controllers\FinalOnsiteReportController;
use App\Http\Controllers\InitialOnsiteReportController;
use App\Http\Controllers\ITServiceController;
use App\Models\FinalOnsiteReport;
use App\Models\InitialOnsiteReport;
use App\Models\ITService;
use App\Models\JobOrder;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('job-orders/it-services')->name('job_order.it_service.')->group(function () {
    Route::get('/', [ITServiceController::class, 'index'])
        ->name('index')
        ->can('viewAny', JobOrder::class);

    Route::post('/', [ITServiceController::class, 'store'])
        ->name('store');

    Route::get('{jobOrder}', [ITServiceController::class, 'edit'])
        ->name('edit')
        ->can('view', [ITService::class, 'jobOrder']);

    Route::prefix('{iTService}/onsites')->name('onsite.')->group(function () {
        Route::get('initial/create', [InitialOnsiteReportController::class, 'create'])
            ->name('initial.create')
            ->can('create', [InitialOnsiteReport::class, 'iTService']);

        Route::post('initial', [InitialOnsiteReportController::class, 'store'])
            ->name('initial.store');

        Route::get('initial/{initialOnsite}', [InitialOnsiteReportController::class, 'showFile'])
            ->name('initial.show_file');

        Route::get('final/create', [FinalOnsiteReportController::class, 'create'])
            ->name('final.create')
            ->can('create', [FinalOnsiteReport::class, 'iTService']);

        Route::post('final', [FinalOnsiteReportController::class, 'store'])
            ->name('final.store');
    });
});
