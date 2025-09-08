<?php

namespace App\Models;

use App\Enums\MachineStatus;
use App\Policies\InitialOnsiteReportPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(InitialOnsiteReportPolicy::class)]
class InitialOnsiteReport extends Model
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
            'recommendation',
            'machine_status',
            'file_name',
            'file_hash',
        ];
    }

    public function itService(): BelongsTo
    {
        return $this->belongsTo(ITService::class, 'it_service_id');
    }
}
