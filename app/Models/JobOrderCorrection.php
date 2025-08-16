<?php

namespace App\Models;

use App\Enums\JobOrderCorrectionRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }
}
