<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeRating extends Pivot
{
    protected $table = 'employee_ratings';

    public function performance(): BelongsTo
    {
        return $this->belongsTo(EmployeePerformance::class);
    }

    public function performanceRating(): BelongsTo
    {
        return $this->belongsTo(PerformanceRating::class, 'performance_rating_id');
    }
}
