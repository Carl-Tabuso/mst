<?php

namespace App\Enums;

enum JobOrderServiceType: string
{
    case Form4     = 'form4';
    case Form5     = 'form5';
    case ITService = 'it_service';

    public function getLabel(): string
    {
        return match ($this) {
            self::Form4     => 'Waste Management',
            self::Form5     => 'Other Services',
            self::ITService => 'IT Services'
        };
    }
}
