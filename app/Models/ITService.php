<?php

namespace App\Models;

use App\Enums\MachineStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ITService extends Model
{
    use HasFactory;

    protected $table = 'it_services';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'machine_status' => MachineStatus::class,
    ];

    public function jobOrder(): MorphOne
    {
        return $this->morphOne(JobOrder::class, 'serviceable');
    }

    public function technicians(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'it_services_technicians',
            'i_t_service_id',
            'technicians'
        );
    }

    public function machineInfos()
    {
        return $this->hasMany(MachineInfo::class, 'it_service_id');
    }
}
