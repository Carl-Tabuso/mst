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
}
