<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobOrderCorrectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'jobOrderId' => $this->job_order_id,
            'status'     => $this->status->getLabel(),
            'properties' => $this->properties,
            'approvedAt' => $this->approved_at,
            'reason'     => $this->reason,
            'createdAt'  => $this->created_at,
            'updatedAt'  => $this->updated_at,
            'jobOrder'   => JobOrderResource::make($this->whenLoaded('jobOrder')),
        ];
    }
}
