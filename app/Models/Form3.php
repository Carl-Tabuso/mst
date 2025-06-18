<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Form3 extends Model
{
    use HasFactory;

    protected $table = 'form3';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function form4(): BelongsTo
    {
        return $this->belongsTo(Form4::class);
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

    public function haulers(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'form3_haulers',
            'form3_id',
            'hauler'
        );
    }
}
