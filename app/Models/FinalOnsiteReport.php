<?php

namespace App\Models;

use App\Enums\MachineStatus;
use App\Policies\FinalOnsiteReportPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(FinalOnsiteReportPolicy::class)]
class FinalOnsiteReport extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'machine_status' => MachineStatus::class,
    ];

    public function attributesForCorrection(): array
    {
        return [
            'service_performed',
            'parts_replaced',
            'remarks',
            'machine_status',
        ];
    }

    public function itService(): BelongsTo
    {
        return $this->belongsTo(ITService::class, 'it_service_id');
    }
}
