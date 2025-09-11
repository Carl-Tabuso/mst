<?php

use App\Http\Controllers\ArchivedEmployeeController;
use App\Http\Controllers\ArchivedJobOrderController;
use App\Http\Controllers\ArchivedUserController;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('archives')->name('archive.')->group(function () {
    Route::prefix('job-orders')->name('job_order.')->group(function () {
        Route::get('/', [ArchivedJobOrderController::class, 'index'])
            ->name('index')
            ->can('viewAny', JobOrder::class);

        Route::patch('bulk-restore', [ArchivedJobOrderController::class, 'bulkRestore'])
            ->name('bulk_restore')
            ->can('restore', JobOrder::class);

        Route::patch('{jobOrder}', [ArchivedJobOrderController::class, 'update'])
            ->name('update')
            ->withTrashed()
            ->can('restore', JobOrder::class);

        Route::delete('{jobOrder}', [ArchivedJobOrderController::class, 'destroy'])
            ->name('destroy')
            ->withTrashed()
            ->can('forceDelete', JobOrder::class);
    });

    Route::prefix('employees')->name('employee.')->group(function () {
        Route::get('/', [ArchivedEmployeeController::class, 'index'])
            ->name('index')
            ->can('viewAny', Employee::class);

        Route::patch('bulk-restore', [ArchivedEmployeeController::class, 'bulkRestore'])
            ->name('bulk_restore')
            ->withTrashed()
            ->can('restore', Employee::class);

        Route::patch('{employee}', [ArchivedEmployeeController::class, 'update'])
            ->name('update')
            ->withTrashed()
            ->can('restore', Employee::class);

        Route::delete('{employee}', [ArchivedEmployeeController::class, 'destroy'])
            ->name('destroy')
            ->withTrashed()
            ->can('forceDelete', Employee::class);
    });

    Route::prefix('users')->name('user.')->group(function () {
        Route::get('/', [ArchivedUserController::class, 'index'])
            ->name('index')
            ->can('viewAny', User::class);

        Route::patch('bulk-restore', [ArchivedUserController::class, 'bulkRestore'])
            ->name('bulk_restore')
            ->withTrashed()
            ->can('restore', User::class);

        Route::patch('{user}', [ArchivedUserController::class, 'update'])
            ->name('update')
            ->withTrashed()
            ->can('restore', User::class);

        Route::delete('{user}', [ArchivedUserController::class, 'destroy'])
            ->name('destroy')
            ->withTrashed()
            ->can('forceDelete', User::class);
    });
});
