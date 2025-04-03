<?php

namespace App\Models;

use App\Enums\JobOrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JobOrder extends Model
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
        'status' => JobOrderStatus::class,
    ];

    public function scopeOfStatuses(Builder $query, JobOrderStatus|string|array $status): Builder
    {
        if ($status instanceof JobOrderStatus) {
            return $query->where('status', $status->value);
        }
        
        if (is_string($status)) {
            return $query->where('status', $status);
        }

        $values = [];

        if (is_array($status)) {
            foreach ($status as $st) {
                $values[] = $st instanceof JobOrderStatus ? $st->value : $st;
            }
        }

        return $query->whereIn('status', $values);
    }

    public function serviceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
