<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PerformanceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
    ];

    protected $casts = [
        'created_at',
        'updated_at',
    ];

    public function teamLeaderPerformanceRatings(): BelongsToMany
    {
        return $this->belongsToMany(PerformanceRating::class)
            ->using(TeamLeaderRating::class);
    }

    public function employeePerformanceRatings(): BelongsToMany
    {
        return $this->belongsToMany(PerformanceRating::class)
            ->using(EmployeeRating::class);
    }
}
