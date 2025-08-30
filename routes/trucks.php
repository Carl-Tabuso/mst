<?php

use App\Http\Controllers\TruckController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('trucks')->name('truck.')->group(function () {
    Route::get('/', [TruckController::class, 'index'])->name('index');
});
