<?php

namespace App\Models;

use App\Enums\HaulingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Form3Hauling extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'date'   => 'datetime',
        'status' => HaulingStatus::class,
    ];

    public function isOpen(): bool
    {
        return today()->lte($this->date) && ! in_array($this->status, [
            HaulingStatus::Done,
            HaulingStatus::InProgress,
        ]);
    }

    public function markAsDone(): void
    {
        $this->update(['status' => HaulingStatus::Done]);
    }

    public function form3(): BelongsTo
    {
        return $this->belongsTo(Form3::class);
    }

    public function assignedPersonnel(): HasOne
    {
        return $this->hasOne(Form3AssignedPersonnel::class);
    }

    public function haulers(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'form3_haulers',
            'form3_hauling_id',
            'hauler'
        );
    }

    public function checklist(): HasOne
    {
        return $this->hasOne(Form3HaulingChecklist::class);
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    public function incident(): HasOne
    {
        return $this->hasOne(Incident::class);
    }

    // add listener for activity logging
}
