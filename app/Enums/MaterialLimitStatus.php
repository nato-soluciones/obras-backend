<?php

namespace App\Enums;

enum MaterialLimitStatus: string
{
    case CRITICAL = 'critical';
    case ALMOST_CRITICAL = 'almost_critical';
    case MINIMUM = 'minimum';
    case ALMOST_MINIMUM = 'almost_minimum';
    case NORMAL = 'normal';
} 