<?php

namespace App\Models;

use App\Enums\IncidentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'occured_at',
        'description',
        'action_taken',
    ];

    protected $casts = [
        'occured_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => IncidentStatus::class,
    ];

    public function involvedEmployees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'incident_employee_involves');
    }

    public function injuredEmployees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'incident_employee_injuries');
    }
}
