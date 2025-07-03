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
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status'     => JobOrderStatus::class,
    ];

    protected $attributes = [
        'status' => JobOrderStatus::ForAppraisal,
    ];

    public function getDeletedAtColumn(): string
    {
        return 'archived_at';
    }

    public function resolveRouteBinding($value, $field = null): Model
    {
        $modelId = (int) str_replace('JO-', '', $value);

        return parent::resolveRouteBinding($modelId, $field);
    }

    public function scopeOfStatuses(Builder $query, JobOrderStatus|array $statuses): Builder
    {
        if ($statuses instanceof JobOrderStatus) {
            return $query->where('status', $statuses->value);
        }

        $values = [];

        foreach ($statuses as $status) {
            $values[] = $status;
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
