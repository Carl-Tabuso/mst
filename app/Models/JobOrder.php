<?php

namespace App\Models;

use App\Collections\JobOrderCollection;
use App\Enums\ActivityLogName;
use App\Enums\JobOrderServiceType;
use App\Enums\JobOrderStatus;
use App\Policies\JobOrderPolicy;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[CollectedBy(JobOrderCollection::class)]
#[UsePolicy(JobOrderPolicy::class)]
class JobOrder extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date_time'        => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'status'           => JobOrderStatus::class,
        'serviceable_type' => JobOrderServiceType::class,
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

    public function getTicketPrefixAttribute(): string
    {
        return $this->ticketPrefix;
    }

    public function getTicketAttribute(): string
    {
        return $this->ticketPrefix.str_pad($this->id, 7, 0, STR_PAD_LEFT);
    }

    public function resolveRouteBinding($value, $field = null): mixed
    {
        return parent::resolveRouteBinding($this->getModelId($value), $field);
    }

    public function resolveSoftDeletableRouteBinding($value, $field = null)
    {
        return parent::resolveRouteBindingQuery($this, $this->getModelId($value), $field)
            ->withTrashed()
            ->first();
    }

    protected function getModelId($value): int
    {
        return (int) str_replace($this->ticketPrefix, '', $value);
    }

    #[Scope]
    public function ofStatuses(Builder $query, JobOrderStatus|array $statuses): Builder
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
    public function ofServiceType(Builder $query, JobOrderServiceType $type): Builder
    {
        return $query->where('serviceable_type', $type->value);
    }

    #[Scope]
    public function cancelled(Builder $query): Builder
    {
        return $query->whereIn('status', JobOrderStatus::getCancelledStatuses());
    }

    #[Scope]
    public function fromPastMonth(Builder $query): Builder
    {
        return $query->whereDate('date_time', '>=', now()->subMonth());
    }

    #[Scope]
    public function fromPastWeek(Builder $query): Builder
    {
        return $query->whereDate('date_time', '>=', now()->subWeek());
    }

    #[Scope]
    public function updatedPastWeekOrMore(Builder $query): Builder
    {
        return $query->whereDate('updated_at', '<=', now()->subWeek());
    }

    public function attributesForCorrection(): array
    {
        return [
            'date_time',
            'client',
            'address',
            'department',
            'contact_no',
            'contact_person',
            'contact_position',
            'description',
        ];
    }

    public function markAsCompleted()
    {
        $this->update(['status' => JobOrderStatus::Completed]);
    }

    public function serviceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by')->withTrashed();
    }

    public function teamLeaderPerformance(): HasOne
    {
        return $this->hasOne(TeamLeaderPerformance::class);
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(JobOrderCorrection::class);
    }

    public function cancel(): HasOne
    {
        return $this->hasOne(CancelledJobOrder::class);
    }

    public function employeePerformances()
    {
        return $this->hasMany(EmployeePerformance::class);
    }

    public function performanceSummary(): HasOne
    {
        return $this->hasOne(PerformanceSummary::class);
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->log_name   = ActivityLogName::from($this->serviceable->getMorphClass())->value;
        $activity->properties = $activity->properties->merge([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function (string $event) {
                $causer            = Auth::user()->employee->full_name ?? 'System';
                $placeholderValues = [
                    'causer' => $causer,
                    'ticket' => $this->ticket,
                ];

                return match ($event) {
                    'created' => __('activity.job_order.created', $placeholderValues),
                    'updated' => __('activity.job_order.updated', $placeholderValues),
                    'deleted' => $this->exists
                        ? __('activity.job_order.archived.single', $placeholderValues)
                        : __('activity.job_order.deleted.single', $placeholderValues),
                    'restored' => __('activity.job_order.restored.single', $placeholderValues),
                };
            });
    }
}
