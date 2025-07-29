<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerformanceSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_order_id',
        'overall_remarks',
    ];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function performances(): HasMany
    {
        return $this->hasMany(EmployeePerformance::class);
    }
}
