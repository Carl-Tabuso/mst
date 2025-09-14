<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeEmploymentDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'employeeId'         => $this->employee_id,
            'sssNumber'          => $this->sss_number,
            'philhealthNumber'   => $this->philhealth_number,
            'pagibigNumber'      => $this->pagibig_number,
            'tin'                => $this->tin,
            'dateHired'          => $this->date_hired,
            'regularizationDate' => $this->regularization_date,
            'endOfContract'      => $this->end_of_contract,
        ];
    }
}
