<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TruckResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'model'      => $this->model,
            'plateNo'    => $this->plate_no,
            'creator'    => EmployeeResource::make($this->whenLoaded('creator')),
            'createdAt'  => $this->created_at,
            'updatedAt'  => $this->updated_at,
            'archivedAt' => $this->archived_at,
        ];
    }
}
