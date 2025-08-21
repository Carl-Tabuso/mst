<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEmploymentDetail extends Model
{
    protected $fillable = [
        'employee_id',
        'sss_number',
        'philhealth_number',
        'pagibig_number',
        'tin',
        'date_hired',
        'regularization_date',
        'end_of_contract'
    ];

    protected $casts = [
        'date_hired' => 'date',
        'regularization_date' => 'date',
        'end_of_contract' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
