<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IncidentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'subject'         => $this->subject,
            'location'        => $this->location,
            'infraction_type' => $this->infraction_type,
            'occured_at'      => $this->occured_at->toIso8601String(),
            'description'     => $this->description,
            'is_read'         => $this->is_read,
            'status'          => $this->status->value,
            'created_by'      => $this->creator ? [
                'id'    => $this->creator->id,
                'name'  => $this->creator->first_name.' '.$this->creator->last_name,
                'email' => $this->creator->email,
            ] : null,

        ];
    }
}
