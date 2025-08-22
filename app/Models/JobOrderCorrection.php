<?php

namespace App\Models;

use App\Enums\JobOrderCorrectionRequestStatus;
use App\Policies\JobOrderCorrectionPolicy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(JobOrderCorrectionPolicy::class)]
class JobOrderCorrection extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'properties' => 'collection',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status'     => JobOrderCorrectionRequestStatus::class,
    ];

    protected $attributes = [
        'status' => JobOrderCorrectionRequestStatus::Pending,
    ];

    public function getDeletedAtColumn(): string
    {
        return 'archived_at';
    }

    #[Scope]
    public function ofStatuses(Builder $query, JobOrderCorrectionRequestStatus|array $requestStatuses): Builder
    {
        if ($requestStatuses instanceof JobOrderCorrectionRequestStatus) {
            return $query->where('status', $requestStatuses->value);
        }

        $values = [];

        foreach ($requestStatuses as $status) {
            $values[] = $status;
        }

        return $query->whereIn('status', $values);
    }

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class)->withTrashed();
    }

    // public function recentRequest(): BelongsTo
    // {
    //     return $this->belongsTo(JobOrder::class)->latestOfMany();
    // }
}
