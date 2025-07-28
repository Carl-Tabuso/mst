<?php

namespace App\Models;

use App\Enums\JobOrderStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
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
        'date_time'  => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status'     => JobOrderStatus::class,
    ];

    protected $attributes = [
        'status' => JobOrderStatus::ForAppraisal,
    ];

    private $ticketPrefix;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->ticketPrefix = 'JO-'.now()->format('y');
    }

    public function getDeletedAtColumn(): string
    {
        return 'archived_at';
    }

    public function getTicketAttribute(): string
    {
        return $this->ticketPrefix.str_pad($this->id, 7, 0, STR_PAD_LEFT);
    }

    public function resolveRouteBinding($value, $field = null): Model
    {
        $modelId = (int) str_replace($this->ticketPrefix, '', $value);

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

    #[Scope]
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

    public function cancel(): HasOne
    {
        return $this->hasOne(CancelledJobOrder::class);
    }
}
