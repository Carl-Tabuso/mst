<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Form4Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'ticket'      => $this->ticket,
            'paymentDate' => $this->payment_date->toISOString(),
            'bidBond'     => $this->bid_bond,
            'orNumber'    => $this->or_number,
            'createdAt'   => $this->created_at,
            'updatedAt'   => $this->updated_at,
            'jobOrder'    => JobOrderResource::make($this->whenLoaded('jobOrder')),
            'appraisers'  => EmployeeResource::collection($this->whenLoaded('appraisers')),
            'form3'       => Form3Resource::make($this->whenLoaded('form3')),
        ];
    }
}
