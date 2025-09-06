<?php

use App\Http\Controllers\ExportTruckController;
use App\Http\Controllers\TruckController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('trucks')->name('truck.')->group(function () {
    Route::get('/', [TruckController::class, 'index'])
        ->name('index')
        ->can('viewAny', 'App\\Models\Truck');

    Route::patch('{truck}', [TruckController::class, 'update'])
        ->name('update');

    Route::post('/', [TruckController::class, 'store'])
        ->name('store');

    Route::get('export', ExportTruckController::class)
        ->name('export');
});
