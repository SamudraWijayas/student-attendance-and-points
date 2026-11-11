<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case HADIR = 'hadir';
    case TERLAMBAT = 'terlambat';

    public function label(): string
    {
        return match ($this) {
            self::HADIR => 'Hadir Tepat Waktu',
            self::TERLAMBAT => 'Terlambat',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::HADIR => 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40',
            self::TERLAMBAT => 'bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/40',
        };
    }
}

