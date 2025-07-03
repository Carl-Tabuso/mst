<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => 'JO-'.str_pad($this->id, 7, 0, STR_PAD_LEFT),
            'serviceableType'       => $this->serviceable_type,
            'serviceableId'         => $this->serviceable_id,
            'dateTime'              => $this->date_time,
            'client'                => $this->client,
            'address'               => $this->address,
            'department'            => $this->department,
            'contactNo'             => $this->contact_no,
            'contactPerson'         => $this->contact_person,
            'contactPosition'       => $this->contact_position,
            'createdBy'             => $this->created_by,
            'status'                => $this->status,
            'errorCount'            => $this->error_count,
            'createdAt'             => $this->created_at,
            'updatedAt'             => $this->updated_at,
            'creator'               => EmployeeResource::make($this->whenLoaded('creator')),
            'serviceable'           => $this->whenLoaded('serviceable', fn () => $this->serviceable->toResource()),
            'teamLeaderPerformance' => TeamLeaderPerformanceResource::make($this->whenLoaded('teamLeaderPerformance')),
            'employeePerformance'   => EmployeePerformanceResource::make($this->whenLoaded('employeePerformance')),
            'corrections'           => JobOrderCorrectionResource::collection($this->whenLoaded('corrections')),
        ];
    }
}
