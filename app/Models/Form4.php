<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Form4 extends Model
{
    use HasFactory;

    protected $table = 'form4';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function jobOrder(): MorphOne
    {
        return $this->morphOne(JobOrder::class, 'serviceable');
    }

    public function form3(): HasOne
    {
        return $this->hasOne(Form3::class);
    }

    public function appraisers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'form4_appraisers');
    }
}
