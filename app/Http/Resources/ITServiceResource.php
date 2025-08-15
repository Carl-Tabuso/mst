<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ITServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'status'         => [
                'value' => $this->status->value,
                'label' => $this->status->getLabel(),
            ],
            'technicianId'   => $this->technician_id,
            'machineType'    => $this->machine_type,
            'model'          => $this->model,
            'serialNo'       => $this->serial_no,
            'tagNo'          => $this->tag_no,
            'machineProblem' => $this->machine_problem,
        ];
    }
}
