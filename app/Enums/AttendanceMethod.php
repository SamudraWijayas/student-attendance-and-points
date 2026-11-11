<?php

namespace App\Enums;

enum AttendanceMethod: string
{
    case QR_CODE = 'qr_code';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::QR_CODE => 'QR Code Scanner',
            self::MANUAL => 'Manual (Admin)',
        };
    }
}

