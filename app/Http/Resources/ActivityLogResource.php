<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'logName'     => $this->log_name,
            'description' => $this->description,
            'event'       => $this->event,
            'batchUuid'   => $this->batch_uuid,
            'createdAt'   => $this->created_at,
            'updatedAt'   => $this->updated_at,
            'causer'      => $this->whenLoaded('causer', fn () => $this->causer->toResource()),
            'subject'     => $this->whenLoaded('subject', fn () => $this->subject->toResource()),
        ];
    }
}
