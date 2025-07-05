<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Form3Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'form4Id'       => $this->form4_id,
            'paymentType'   => $this->payment_type,
            'appraisedDate' => $this->appraised_date?->toISOString(),
            'approvedDate'  => $this->approved_date?->toISOString(),
            'from'          => $this->from,
            'to'            => $this->to,
            'createdAt'     => $this->created_at,
            'updatedAt'     => $this->updated_at,
            'form4'         => Form4Resource::make($this->whenLoaded('form4')),
            'haulings'      => Form3HaulingResource::collection($this->whenLoaded('haulings')),
        ];
    }
}
