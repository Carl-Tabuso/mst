<?php

use App\Http\Controllers\ExportTruckController;
use App\Http\Controllers\TruckController;
use App\Models\Truck;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('trucks')->name('truck.')->group(function () {
    Route::get('/', [TruckController::class, 'index'])
        ->name('index')
        ->can('viewAny', Truck::class);

    Route::patch('{truck}', [TruckController::class, 'update'])
        ->name('update');

    Route::delete('{truck}', [TruckController::class, 'destroy'])
        ->name('destroy')
        ->can('delete', Truck::class);

    Route::post('/', [TruckController::class, 'store'])
        ->name('store');

    Route::get('export', ExportTruckController::class)
        ->name('export')
        ->can('viewAny', Truck::class);
});
