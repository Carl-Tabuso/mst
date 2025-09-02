<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function itService(): BelongsTo
    {
        return $this->belongsTo(ITService::class, 'it_service_id');
    }
}
