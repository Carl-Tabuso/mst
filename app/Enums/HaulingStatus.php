<?php

namespace App\Enums;

enum HaulingStatus: string
{
    case ForPersonnelAssignment = 'for personnel assignment';
    case ForSafetyInspection    = 'for safety inspection';
    case InProgress             = 'in progress';
    case Done                   = 'done';
}
