<?php
namespace App\Enums;

enum ITServiceStatus: string {
    case ForCheckUp       = 'For Check Up';
    // case WaitingQuotation = 'Waiting Quotation';
    case ForFinalService  = 'For Final Service';
    case Completed        = 'Completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::ForCheckUp => 'For Check Up',
            // self::WaitingQuotation => 'Waiting for Quotation',
            self::ForFinalService => 'For Final Service',
            self::Completed => 'Completed',
        };
    }
}
