<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Form3AssignedPersonnel extends Model
{
    use HasFactory;

    protected $table = 'form3_assigned_personnels';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function hauling(): BelongsTo
    {
        return $this->belongsTo(Form3Hauling::class);
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'team_leader');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'team_driver');
    }

    public function safetyOfficer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'safety_officer');
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'team_mechanic');
    }
}
