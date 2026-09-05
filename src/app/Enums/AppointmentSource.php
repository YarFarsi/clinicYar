<?php

namespace App\Enums;

enum AppointmentSource: string
{
    case Manual = 'manual';
    case Website = 'website';
    case Phone = 'phone';
    case Online = 'online';
    case Integration = 'integration';
}
