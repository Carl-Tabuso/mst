<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEmergencyContact extends Model
{
    protected $fillable = [
        'employee_id',
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'contact_number',
        'relation',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
