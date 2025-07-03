<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Form3Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'form4Id'         => $this->form4_id,
            'truckNo'         => $this->truck_no,
            'paymentType'     => $this->payment_type,
            'appraisedDate'   => $this->appraised_date?->toISOString(),
            'approvedDate'    => $this->approved_date?->toISOString(),
            'from'            => $this->from,
            'to'              => $this->to,
            'teamLeaderId'    => $this->team_leader,
            'teamDriverId'    => $this->team_driver,
            'safetyOfficerId' => $this->safety_officer,
            'teamMechanicId'  => $this->team_mechanic,
            'createdAt'       => $this->created_at,
            'updatedAt'       => $this->updated_at,
            'form4'           => Form4Resource::make($this->whenLoaded('form4')),
            'teamLeader'      => EmployeeResource::make($this->whenLoaded('teamLeader')),
            'teamDriver'      => EmployeeResource::make($this->whenLoaded('teamDriver')),
            'safetyOfficer'   => EmployeeResource::make($this->whenLoaded('safetyOfficer')),
            'teamMechanic'    => EmployeeResource::make($this->whenLoaded('teamMechanic')),
            'haulers'         => EmployeeResource::collection($this->whenLoaded('haulers')),
        ];
    }
}
