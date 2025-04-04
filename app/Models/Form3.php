<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function hauling(): HasOne
    {
        return $this->hasOne(Form3Hauling::class);
    }
}
