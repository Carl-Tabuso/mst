<?php

namespace App\Models;

use App\Enums\JobOrderStatus;
use App\Policies\ITServicePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[UsePolicy(ITServicePolicy::class)]
class ITService extends Model
{
    use HasFactory;

    protected $table = 'it_services';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function attributesForCorrection(): array
    {
        return [
            'machine_type',
            'model',
            'serial_no',
            'tag_no',
            'machine_problem',
            'technician_id',
        ];
    }

    public function isForFinalServiceOrCompleted(): bool
    {
        return in_array($this->jobOrder->status, [
            JobOrderStatus::ForFinalService,
            JobOrderStatus::Completed,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->jobOrder->status === JobOrderStatus::Completed;
    }

    public function jobOrder(): MorphOne
    {
        return $this->morphOne(JobOrder::class, 'serviceable');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'technician_id');
    }

    public function initialOnsiteReport(): HasOne
    {
        return $this->hasOne(InitialOnsiteReport::class, 'it_service_id');
    }

    public function finalOnsiteReport(): HasOne
    {
        return $this->hasOne(FinalOnsiteReport::class, 'it_service_id');
    }
}
