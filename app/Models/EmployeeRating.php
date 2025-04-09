<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeRating extends Pivot
{
    protected $table = 'employee_ratings';

    public function performance(): BelongsTo
    {
        return $this->belongsTo(EmployeePerformance::class);
    }
}
