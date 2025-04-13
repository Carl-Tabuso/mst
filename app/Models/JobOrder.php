<?php

namespace App\Models;

use App\Enums\JobOrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JobOrder extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => JobOrderStatus::class,
    ];

    public function scopeOfStatuses(Builder $query, JobOrderStatus|array $status): Builder
    {
        if ($status instanceof JobOrderStatus) {
            return $query->where('status', $status->value);
        }

        $values = [];

        if (is_array($status)) {
            foreach ($status as $st) {
                $values[] = $st->value;
            }
        }

        return $query->whereIn('status', $values);
    }

    public function cancelled(Builder $query): Builder
    {
        return $query->whereIn('status', JobOrderStatus::getCancelledStatuses());   
    }

    public function serviceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function teamLeaderPerformance(): HasOne
    {
        return $this->hasOne(TeamLeaderPerformance::class);
    }

    public function employeePerformance(): HasOne
    {
        return $this->hasOne(EmployeePerformance::class);
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(JobOrderCorrection::class);
    }
}
