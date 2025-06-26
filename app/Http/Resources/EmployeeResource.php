<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'firstName'  => $this->first_name,
            'middleName' => $this->whenNotNull($this->middle_name),
            'lastName'   => $this->last_name,
            'suffix'     => $this->whenNotNull($this->suffix),
            'fullName'   => $this->full_name,
            'positionId' => $this->position_id,
            'createdAt'  => $this->created_at,
            'updatedAt'  => $this->updated_at,
            'account'    => UserResource::make($this->whenLoaded('account')),
        ];
    }
}
