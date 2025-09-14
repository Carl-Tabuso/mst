<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeEmergencyContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'employeeId'    => $this->employee_id,
            'lastName'      => $this->last_name,
            'firstName'     => $this->first_name,
            'middleName'    => $this->middle_name,
            'suffix'        => $this->suffix,
            'contactNumber' => $this->contact_number,
            'relation'      => $this->relation,
        ];
    }
}
