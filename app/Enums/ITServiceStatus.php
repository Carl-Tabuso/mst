<?php

namespace App\Enums;

enum ITServiceStatus: string
{
    case ForCheckUp       = 'for check up';
    // case WaitingQuotation = 'Waiting Quotation';
    case ForFinalService  = 'for final service';
    case Completed        = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::ForCheckUp => 'For Check Up',
            // self::WaitingQuotation => 'Waiting for Quotation',
            self::ForFinalService => 'For Final Service',
            self::Completed       => 'Completed',
        };
    }
}
