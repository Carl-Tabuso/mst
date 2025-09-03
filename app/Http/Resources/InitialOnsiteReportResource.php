<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InitialOnsiteReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'servicePerformed' => $this->service_performed,
            'recommendation'   => $this->recommendation,
            'machineStatus'    => $this->machine_status,
            'fileName'         => $this->file_name,
            'fileHashed'       => $this->file_hashed,
            'createdAt'        => $this->created_at,
            'updatedAt'        => $this->updated_at,
            'itService'        => ITServiceResource::make($this->whenLoaded('itService')),
        ];
    }
}
