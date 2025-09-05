<?php

namespace App\Models;

use App\Policies\TruckPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(TruckPolicy::class)]
class Truck extends Model
{
    use HasFactory;

    protected $guarded = [
        'created_at',
        'updated_at',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'added_by')->withTrashed();
    }

    public function haulings(): HasMany
    {
        return $this->hasMany(Form3Hauling::class);
    }
}
