<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Form3HaulingChecklistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'form3HaulingId'            => $this->form3_hauling_id,
            'isVehicleInspectionFilled' => $this->is_vehicle_inspection_filled,
            'isUniformPpeFilled'        => $this->is_uniform_ppe_filled,
            'isToolsEquipmentFilled'    => $this->is_tools_equipment_filled,
            'isCertified'               => $this->is_certify,
            'createdAt'                 => $this->created_at,
            'updatedAt'                 => $this->updated_at,
        ];
    }
}
