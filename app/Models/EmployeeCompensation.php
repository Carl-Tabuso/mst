<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCompensation extends Model
{
    protected $fillable = [
        'employee_id',
        'salary',
        'allowance'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
