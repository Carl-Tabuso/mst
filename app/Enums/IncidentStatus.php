<?php

namespace App\Enums;

enum IncidentStatus: string
{
    case ForVerification = 'for verification';
    case Verified        = 'verified';
    case Dropped         = 'dropped';

    case NoIncident      = 'no incident';

    case Draft           = 'draft';

    public function getLabel(): string
    {
        return match ($this) {
            self::ForVerification => 'For Verification',
            self::Verified        => 'Verified',
            self::Dropped         => 'Dropped',
            self::Draft           => 'Draft',
            self::NoIncident      => 'No Incident',
        };
    }
}