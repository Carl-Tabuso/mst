<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'ticket'                => $this->ticket,
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
            'teamLeaderPerformance' => TeamLeaderPerformanceResource::make($this->whenLoaded('teamLeaderPerformance')),
            'employeePerformance'   => EmployeePerformanceResource::make($this->whenLoaded('employeePerformance')),
            'corrections'           => JobOrderCorrectionResource::collection($this->whenLoaded('corrections')),
            'cancel'                => CancelledJobOrderResource::make($this->whenLoaded('cancel')),
            'itServiceStatus' => $this->whenLoaded('serviceable', function () {
                if ($this->serviceable instanceof \App\Models\ITService) {
                    return $this->serviceable->status;
                }

                return null;
            }),
            'itServiceStatusLabel' => $this->whenLoaded('serviceable', function () {
                if ($this->serviceable instanceof \App\Models\ITService  && $this->serviceable->status instanceof \App\Enums\ITServiceStatus) {
                    return $this->serviceable->status->getLabel();
                }
                return null;
            }),
            'serviceable' => $this->whenLoaded('serviceable', function () {
                $serviceable = $this->serviceable;

                return array_merge(
                    $serviceable->asResource()->toArray(request()),
                    [
                        'status' => [
                            'value' => $serviceable?->status->value ?? null,
                        ],
                        'reportInitial' => $serviceable instanceof \App\Models\ITService
                            ? $serviceable->reports()->where('onsite_type', 'initial')->latest()->first()
                            : null,
                        'reportFinal' => $serviceable instanceof \App\Models\ITService
                            ? $serviceable->reports()->where('onsite_type', 'final')->latest()->first()
                            : null,
                    ]
                );
            }),

        ];
    }
}
