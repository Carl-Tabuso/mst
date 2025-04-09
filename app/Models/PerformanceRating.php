<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PerformanceRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'scale',
        'name',
    ];

    protected $casts = [
        'created_at',
        'updated_at',
    ];

    public function teamLeaderPerformanceCategories(): BelongsToMany
    {
        return $this->belongsToMany(PerformanceCategory::class)
            ->using(TeamLeaderPerformance::class);
    }

    public function employeePerformanceCategories(): BelongsToMany
    {
        return $this->belongsToMany(PerformanceCategory::class)
            ->using(EmployeePerformance::class);
    }
}
