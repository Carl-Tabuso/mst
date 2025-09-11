<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'firstName'         => $this->first_name,
            'middleName'        => $this->whenNotNull($this->middle_name),
            'lastName'          => $this->last_name,
            'suffix'            => $this->whenNotNull($this->suffix),
            'dateOfBirth'       => $this->whenNotNull($this->date_of_birth),
            'email'             => $this->whenNotNull($this->email),
            'contactNumber'     => $this->whenNotNull($this->contact_number),
            'fullName'          => $this->full_name,
            'positionId'        => $this->position_id,
            'positionName'      => $this->whenLoaded('position', fn () => $this->position->name),
            'createdAt'         => $this->created_at,
            'updatedAt'         => $this->updated_at,
            'account'           => UserResource::make($this->whenLoaded('account')),
            'accountCreatedAt'  => $this->whenLoaded('account', fn () => $this->account->created_at),
            'accountStatus'     => $this->whenLoaded('account', fn () => $this->account->deleted_at ? 'Deactivated' : 'Active'),
            'region'            => $this->region,
            'province'          => $this->province,
            'city'              => $this->city,
            'zipCode'           => $this->zip_code,
            'detailedAddress'   => $this->detailed_address,
            'emergencyContact'  => EmployeeEmergencyContactResource::make($this->whenLoaded('emergencyContact')),
            'employmentDetails' => EmployeeEmploymentDetailResource::make($this->whenLoaded('employmentDetails')),
            'compensation'      => EmployeeCompensationResource::make($this->whenLoaded('compensation')),
        ];
    }
}
