<?php

namespace App\Enums;

enum MachineStatus: string
{
    case CompleteRepair = 'complete repair';
    case PendingRepair = 'pending repair';
    case ForPullOut = 'pull out';
    case Irrepairable = 'irrepairable';
    case Others = 'others';

    public function getLabel(): string
    {
        return match ($this) {
            self::CompleteRepair => 'Complete Repair',
            self::PendingRepair => 'Pending Repair',
            self::ForPullOut => 'For Pull Out / Shop Repair',
            self::Irrepairable => 'Irrepairable',
            self::Others => 'Others',
        };
    }
}