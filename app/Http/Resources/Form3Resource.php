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
            'truckNo'       => $this->truck_no,
            'paymentType'   => $this->payment_type,
            'appraisedDate' => $this->appraised_date,
            'approvedDate'  => $this->approved_date,
            'from'          => $this->from,
            'to'            => $this->to,
            'teamLeader'    => $this->team_leader,
            'teamDriver'    => $this->team_driver,
            'safetyOfficer' => $this->safety_officer,
            'teamMechanic'  => $this->team_mechanic,
            'createdAt'     => $this->created_at,
            'updatedAt'     => $this->updated_at,
        ];
    }
}
