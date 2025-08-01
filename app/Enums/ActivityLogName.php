<?php

namespace App\Enums;

enum ActivityLogName: string
{
    case Auth            = 'authentication';
    case WasteManagement = 'waste management';
    case ITService       = 'it_service';
    case OtherService    = 'others';
}
