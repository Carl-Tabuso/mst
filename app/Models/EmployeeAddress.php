<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAddress extends Model
{
    protected $fillable = [
        'employee_id',
        'region',
        'province',
        'city',
        'zip_code',
        'detailed_address',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
