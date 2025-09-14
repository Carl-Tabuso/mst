<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ITServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'technicianId'        => $this->technician_id,
            'machineType'         => $this->machine_type,
            'model'               => $this->model,
            'serialNo'            => $this->serial_no,
            'tagNo'               => $this->tag_no,
            'machineProblem'      => $this->machine_problem,
            'jobOrder'            => JobOrderResource::make($this->whenLoaded('jobOrder')),
            'initialOnsiteReport' => InitialOnsiteReportResource::make($this->whenLoaded('initialOnsiteReport')),
            'finalOnsiteReport'   => FinalOnsiteReportResource::make($this->whenLoaded('finalOnsiteReport')),
            'technician'          => EmployeeResource::make($this->whenLoaded('technician')),
        ];
    }
}
