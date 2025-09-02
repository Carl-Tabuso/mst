<?php

namespace App\Enums;

enum MachineStatus: string
{
    case CompleteRepair = 'complete repair';
    case PendingRepair  = 'pending repair';
    case ForPullOut     = 'pull out';
    case Irrepairable   = 'irrepairable';
    case Others         = 'others';

    public function getLabel(): string
    {
        return match ($this) {
            self::CompleteRepair => 'Complete Repair',
            self::PendingRepair  => 'Pending Repair',
            self::ForPullOut     => 'For Pull Out / Shop Repair',
            self::Irrepairable   => 'Irrepairable',
            self::Others         => 'Others',
        };
    }

    public static function allAsOptions(): array
    {
        return collect(self::cases())->map(fn (self $status) => [
            'label' => $status->getLabel(),
            'value' => $status->value,
        ])->toArray();
    }

    public static function forFirstOnsite(): array
    {
        return collect([
            self::CompleteRepair,
            self::PendingRepair,
            self::ForPullOut,
            self::Irrepairable,
            self::Others,
        ])->map(fn (self $status) => [
            'label' => $status->getLabel(),
            'value' => $status->value,
        ])->toArray();
    }

    public static function forFinalOnsite(): array
    {
        return collect([
            self::CompleteRepair,
            self::PendingRepair,
            self::ForPullOut,
            self::Irrepairable,
            self::Others,
        ])->map(fn (self $status) => [
            'label' => $status->getLabel(),
            'value' => $status->value,
        ])->toArray();
    }
}
