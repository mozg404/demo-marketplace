<?php

namespace App\Enum;

enum TimeToLive: int
{
    case Minute = 60;
    case Hour = 3600;
    case TwoHours = 7200;
    case ThreeHours = 1440;
    case Day = 86400;
    case TwoDays = 172800;
    case ThreeDays = 345600;
    case Week = 604800;
}
