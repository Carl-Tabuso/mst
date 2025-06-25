<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePerformanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'jobOrderId'  => $this->job_order_id,
            'evaluatorId' => $this->evaluator_id,
            'evaluateeId' => $this->evaluatee_id,
            'createdAt'   => $this->created_at,
            'updatedAt'   => $this->updated_at,
            'jobOrder'    => JobOrderResource::make($this->whenLoaded('jobOrder')),
            'evaluator'   => EmployeeResource::make($this->whenLoaded('evaluator')),
            'evaluatee'   => EmployeeResource::make($this->whenLoaded('evaluatee')),
            'ratings'     => EmployeeRatingResource::collection($this->whenLoaded('ratings')),
        ];
    }
}
