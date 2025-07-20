<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Form3AssignedPersonnelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'form3HaulingId'  => $this->form3_hauling_id,
            'createdAt'       => $this->created_at,
            'updatedAt'       => $this->updated_at,
            'hauling'         => Form3HaulingResource::make($this->whenLoaded('hauling')),
            'teamLeader'      => EmployeeResource::make($this->whenLoaded('teamLeader')),
            'teamDriver'      => EmployeeResource::make($this->whenLoaded('teamDriver')),
            'safetyOfficer'   => EmployeeResource::make($this->whenLoaded('safetyOfficer')),
            'teamMechanic'    => EmployeeResource::make($this->whenLoaded('teamMechanic')),
        ];
    }
}
