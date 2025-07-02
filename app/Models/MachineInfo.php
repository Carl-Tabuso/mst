<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineInfo extends Model
{
    use HasFactory;

    protected $table = 'machine_infos';

    protected $fillable = [
        'machine_type',
        'model',
        'serial_no',
        'tag_no',
        'machine_problem',
        'service_performed',
        'recommendation',
        'machine_status',
    ];

    // Example relationship: One ITService may reference this MachineInfo
    public function itService()
    {
        return $this->belongsTo(ITService::class, 'it_service_id');
    }
}
