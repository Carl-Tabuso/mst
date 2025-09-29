<?php

namespace App\Models;

use App\Enums\ActivityLogName;
use App\Policies\TruckPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[UsePolicy(TruckPolicy::class)]
class Truck extends Model
{
    use HasFactory, LogsActivity;

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

    public function tapActivity(Activity $activity): void
    {
        $activity->log_name   = ActivityLogName::Truck->value;
        $activity->properties = $activity->properties->merge([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function (string $event) {
                $causer = Auth::user()->employee->full_name ?? 'System';

                return match ($event) {
                    'created' => __('activity.truck.created', ['causer' => $causer]),
                    'updated' => __('activity.truck.updated', ['causer' => $causer]),
                };
            });
    }
}
