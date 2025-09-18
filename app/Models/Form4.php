<?php

namespace App\Models;

use App\Policies\WasteManagementPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[UsePolicy(WasteManagementPolicy::class)]
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
        'bid_bond'     => 'double',
        'payment_date' => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function attributesForCorrection(): array
    {
        return [
            'payment_date',
            'bid_bond',
            'or_number',
        ];
    }

    public function jobOrder(): MorphOne
    {
        return $this->morphOne(JobOrder::class, 'serviceable')
            ->withTrashed();
    }

    public function form3(): HasOne
    {
        return $this->hasOne(Form3::class);
    }

    public function appraisers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'form4_appraisers')
            ->withTrashed();
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'form_dispatcher')
            ->withTrashed();
    }
}
