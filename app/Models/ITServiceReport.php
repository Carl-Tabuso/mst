<?php
namespace App\Models;

use App\Enums\MachineStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ITServiceReport extends Model
{
    use HasFactory;

    protected $table = 'it_service_reports';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'machine_status' => MachineStatus::class,
        'final_machine_status' => MachineStatus::class,
    ];

    public function itService(): BelongsTo
    {
        return $this->belongsTo(ITService::class);
    }
}
