<?php

namespace App\Http\Resources;

use App\Enums\ActivityLogName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Agent\Facades\Agent;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'time'        => $this->created_at->format('g:i A'),
            'humanDiff'   => $this->created_at->diffForHumans(),
            'log'         => ActivityLogName::from($this->log_name)->getLabel(),
            'description' => $this->description,
            'ipAddress'   => $this->properties['ip_address'],
            'browser'     => Agent::browser($this->properties['user_agent']),
            'platform'    => Agent::platform($this->properties['user_agent']),
            'causer'      => $this->when($this->causer,
                fn () => $this->whenLoaded('causer', fn () => $this->causer->toResource())
            ),
        ];
    }
}
