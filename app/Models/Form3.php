<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'appraised_date' => 'date',
        'approved_date'  => 'date',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    public function attributesForCorrection(): array
    {
        return [
            'approved_date',
            'payment_type',
            'appraised_date',
        ];
    }

    public function form4(): BelongsTo
    {
        return $this->belongsTo(Form4::class);
    }

    public function haulings(): HasMany
    {
        return $this->hasMany(Form3Hauling::class);
    }
    public function haulers()
    {
        return $this->belongsToMany(
            Employee::class,
            'form3_haulers',
            'form3_hauling_id',
            'hauler'           
        );
    }

    public function assignedPersonnel()
{
    return $this->hasOne(Form3AssignedPersonnel::class, 'form3_hauling_id');
}

public function teamLeader()
{
    return $this->hasOneThrough(
        Employee::class,
        Form3AssignedPersonnel::class,
        'form3_hauling_id', // FK on form3_assigned_personnels
        'id',               // PK on employees
        'id',               // local key on form3
        'team_leader'       // FK column in form3_assigned_personnels
    );
}

public function driver()
{
    return $this->hasOneThrough(
        Employee::class,
        Form3AssignedPersonnel::class,
        'form3_hauling_id',
        'id',
        'id',
        'team_driver'
    );
}
}
