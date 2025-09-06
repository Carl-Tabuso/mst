<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinalOnsiteReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'servicePerformed' => $this->service_performed,
            'partsReplaced'    => $this->parts_replaced,
            'remarks'          => $this->remarks,
            'machineStatus'    => $this->machine_status,
            'createdAt'        => $this->created_at,
            'updatedAt'        => $this->updated_at,
            'itService'        => ITServiceResource::make($this->whenLoaded('itService')),
        ];
    }
}
