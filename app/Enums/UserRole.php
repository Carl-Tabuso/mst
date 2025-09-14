<?php

namespace App\Enums;

enum UserRole: string
{
    case Frontliner     = 'frontliner';
    case Dispatcher     = 'dispatcher';
    case TeamLeader     = 'team leader';
    case HeadFrontliner = 'head frontliner';
    case SafetyOfficer  = 'safety officer';
    case HumanResource  = 'human resource';
    case Consultant     = 'consultant';
    case Regular        = 'regular';
    case ITAdmin        = 'it admin';

    public function getLabel(): string
    {
        return match ($this) {
            self::Frontliner     => 'Frontliner',
            self::Dispatcher     => 'Dispatcher',
            self::TeamLeader     => 'Team Leader',
            self::HeadFrontliner => 'Head Frontliner',
            self::SafetyOfficer  => 'Safety Officer',
            self::HumanResource  => 'Human Resource',
            self::Consultant     => 'Consultant',
            self::Regular        => 'Basic User',
            self::ITAdmin        => 'IT Admin',
        };
    }
}
