<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Form3HaulingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'form3Id'           => $this->form3_id,
            'truckNo'           => $this->truck_no,
            'date'              => $this->date,
            'form3'             => Form3Resource::make($this->whenLoaded('form3')),
            'assignedPersonnel' => Form3AssignedPersonnelResource::make($this->whenLoaded('assignedPersonnel')),
            'haulers'           => EmployeeResource::collection($this->whenLoaded('haulers')),
            'checklist'         => Form3HaulingChecklistResource::make($this->whenLoaded('checklist')),
            'isOpen'            => $this->isOpen(),
        ];
    }
}
