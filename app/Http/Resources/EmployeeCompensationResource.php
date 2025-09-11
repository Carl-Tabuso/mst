<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeCompensationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'employeeId' => $this->employee_id,
            'salary'     => $this->salary,
            'allowance'  => $this->allowance,
        ];
    }
}
