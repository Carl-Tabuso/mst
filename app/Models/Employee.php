<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'full_name',
    ];

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => implode(" ",
                array_filter([
                    $attributes['first_name'],
                    $attributes['middle_name'],
                    $attributes['last_name'],
                    $attributes['suffix'],
                ])
            )
        );
    }

    public function account(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function involveIncidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_employee_involves');
    }

    public function injuryIncidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_employee_injuries');
    }

    public function appraisedForm4s(): BelongsToMany
    {
        return $this->belongsToMany(Form4::class, 'form4_appraisers');
    }

    public function form5sAssignee(): HasMany
    {
        return $this->hasMany(Form5::class, 'assigned_person');
    }

    public function form3sHaulee(): BelongsToMany
    {
        return $this->belongsToMany(
            Form3Hauling::class,
            'form3_haulers',
            'hauler',
            'form3_hauling_id'
        );
    }

    public function createdJobOrders(): HasMany
    {
        return $this->hasMany(JobOrder::class, 'created_by');
    }

    public function diagnosticITServices(): HasMany
    {
        return $this->hasMany(ITService::class, 'cse');
    }
}
