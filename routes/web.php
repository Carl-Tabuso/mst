<?php

use App\Http\Controllers\JobOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::inertia('/', 'Home')->name('home');

    Route::prefix('job-orders')->name('job_order.')->group(function () {
        Route::get('/', [JobOrderController::class, 'index'])->name('index');
        Route::get('waste-managements', fn () => dd('wm'))->name('waste_management');
        Route::get('it-services', fn () => dd('it'))->name('it_service');
        Route::get('others', fn () => dd('os'))->name('others');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
