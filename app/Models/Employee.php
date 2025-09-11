<?php

namespace App\Models;

use App\Policies\EmployeePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(EmployeePolicy::class)]
class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'date_of_birth',
        'email',
        'contact_number',
        'position_id',
        'region',
        'province',
        'city',
        'zip_code',
        'detailed_address',
    ];

    protected $casts = [
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'date_of_birth' => 'date',
    ];

    protected $appends = [
        'full_name',
    ];

    public function getDeletedAtColumn(): string
    {
        return 'archived_at';
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => implode(' ',
                array_filter([
                    $this->first_name,
                    $this->middle_name,
                    $this->last_name,
                    $this->suffix,
                ])
            )
        );
    }

    public function emergencyContact()
    {
        return $this->hasOne(EmployeeEmergencyContact::class);
    }

    public function employmentDetails()
    {
        return $this->hasOne(EmployeeEmploymentDetail::class);
    }

    public function compensation()
    {
        return $this->hasOne(EmployeeCompensation::class);
    }

    public function account(): HasOne
    {
        return $this->hasOne(User::class)->withTrashed();
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

    public function form3sHauler(): BelongsToMany
    {
        return $this->belongsToMany(
            Form3Hauling::class,
            'form3_haulers',
            'hauler',
            'form3_id'
        );
    }

    public function itServicesAsTechnician(): BelongsToMany
    {
        return $this->belongsToMany(
            ITService::class,
            'it_services_technicians',
            'technicians',
            'it_service_id'
        );
    }

    public function createdJobOrders(): HasMany
    {
        return $this->hasMany(JobOrder::class, 'created_by')->withTrashed();
    }

    public function form3sAsTeamLeader(): HasMany
    {
        return $this->hasMany(Form3::class, 'team_leader');
    }

    public function form3sAsDriver(): HasMany
    {
        return $this->hasMany(Form3::class, 'team_driver');
    }

    public function form3sAsSafetyOfficer(): HasMany
    {
        return $this->hasMany(Form3::class, 'safety_officer');
    }

    public function form3sAsMechanic(): HasMany
    {
        return $this->hasMany(Form3::class, 'team_mechanic');
    }

    public function evaluatedEmployees(): HasMany
    {
        return $this->hasMany(EmployeePerformance::class, 'evaluator_id');
    }

    public function performancesAsEmployee(): HasMany
    {
        return $this->hasMany(EmployeePerformance::class, 'evaluatee_id');
    }

    public function evaluatedTeamLeaders(): HasMany
    {
        return $this->hasMany(TeamLeaderPerformance::class, 'evaluator_id');
    }

    public function performancesAsTeamLeader(): HasMany
    {
        return $this->hasMany(TeamLeaderPerformance::class, 'evaluatee_id');
    }
}
