<?php

namespace App\Models;

use App\Enums\IncidentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_order_id',
        'form3_hauling_id',
        'created_by',
        'subject',
        'location',
        'infraction_type',
        'occured_at',
        'description',
        'action_taken',
        'status',
        'is_read',
    ];

    protected $casts = [
        'occured_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status'     => IncidentStatus::class,
        'is_read'    => 'boolean',
    ];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function hauling(): BelongsTo
    {
        return $this->belongsTo(Form3Hauling::class, 'form3_hauling_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function involvedEmployees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'incident_employee_involves');
    }

    public function injuredEmployees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'incident_employee_injuries');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}
