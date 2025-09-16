<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Collections\UserCollection;
use App\Notifications\VerifyEmailWithPassword;
use App\Policies\UserPolicy;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

#[CollectedBy(UserCollection::class)]
#[UsePolicy(UserPolicy::class)]
class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'email',
        'password',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    protected $guard_name = 'web';

    public function sendEmailVerificationNotificationWithPassword($password)
    {
        $this->notify(new VerifyEmailWithPassword($password));
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logExcept(['password', 'remember_token'])
    //         ->logFillable()
    //         ->logOnlyDirty()
    //         ->dontLogIfAttributesChangedOnly(['remember_token']);
    // }
}
