<?php

namespace App\Models;

use App\Enums\JobOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ITService extends Model
{
    use HasFactory;

    protected $table = 'it_services';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status'     => JobOrderStatus::class,
    ];

    protected $appends = ['current_status'];

    public function jobOrder(): MorphOne
    {
        return $this->morphOne(JobOrder::class, 'serviceable');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'technician_id');
    }

    // public function reports(): HasMany
    // {
    //     return $this->hasMany(ITServiceReport::class, 'it_service_id');
    // }

    // public function firstOnsiteReport()
    // {
    //     return $this->hasOne(ITServiceReport::class)->where('onsite_type', 'Initial');
    // }

    // public function finalOnsiteReport()
    // {
    //     return $this->hasOne(ITServiceReport::class)->where('onsite_type', 'Final');
    // }

    public function initialOnsiteReport()
    {
        return $this->hasOne(InitialOnsiteReport::class, 'it_service_id');
    }

    public function finalOnsiteReport()
    {
        return $this->hasOne(FinalOnsiteReport::class, 'it_service_id');
    }

    // Individual scope methods for better maintainability
    public function scopeForCheckup($query)
    {
        return $query->whereDoesntHave('reports');
    }

    public function scopeForFinalService($query)
    {
        return $query->whereHas('reports', function ($q) {
            $q->where('onsite_type', 'Initial');
        })->where(function ($q) {
            $q->whereDoesntHave('reports', function ($subQ) {
                $subQ->where('onsite_type', 'Final');
            })->orWhereHas('reports', function ($subQ) {
                $subQ->where('onsite_type', 'Final')
                    ->where('final_machine_status', '!=', 'complete repair');
            });
        });
    }

    public function scopeCompleted($query)
    {
        return $query->whereHas('reports', function ($q) {
            $q->where('onsite_type', 'Final')
                ->where('final_machine_status', 'complete repair');
        });
    }

    public function scopeByStatus($query, array $statuses)
    {
        return $query->where(function ($statusQuery) use ($statuses) {
            foreach ($statuses as $status) {
                $statusQuery->orWhere(function ($individualStatusQuery) use ($status) {
                    switch ($status) {
                        case 'for check up':
                            $individualStatusQuery->forCheckup();
                            break;

                        case 'for final service':
                            $individualStatusQuery->forFinalService();
                            break;

                        case 'completed':
                            $individualStatusQuery->completed();
                            break;
                    }
                });
            }
        });
    }

    public function getCurrentStatus(): string
    {
        if (! $this->reports || $this->reports->isEmpty()) {
            return 'for check up';
        }

        $finalReport = $this->reports->where('onsite_type', 'Final')->first();

        if (! $finalReport) {
            return 'for final service';
        }

        if ($finalReport->final_machine_status === 'complete repair') {
            return 'completed';
        }

        return 'for final service';
    }

    public function getCurrentStatusAttribute(): string
    {
        return $this->getCurrentStatus();
    }
}
