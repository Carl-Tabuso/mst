<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'avatar'          => $this->avatar,
            'employeeId'      => $this->employee_id,
            'email'           => $this->email,
            'emailVerifiedAt' => $this->email_verified_at,
            'createdAt'       => $this->created_at,
            'updatedAt'       => $this->updated_at,
            'deletedAt'       => $this->deleted_at,
            'employee'        => EmployeeResource::make($this->whenLoaded('employee')),
            'roles'           => RoleResource::collection($this->roles),
        ];
    }
}
