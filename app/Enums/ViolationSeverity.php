<?php

namespace App\Enums;

enum ViolationSeverity: string
{
    case RINGAN = 'ringan';
    case SEDANG = 'sedang';
    case BERAT = 'berat';
    case FATAL = 'fatal';

    public function label(): string
    {
        return match ($this) {
            self::RINGAN => 'Ringan',
            self::SEDANG => 'Sedang',
            self::BERAT => 'Berat',
            self::FATAL => 'Fatal',
        };
    }

    public function defaultRange(): string
    {
        return match ($this) {
            self::RINGAN => '-5 Poin',
            self::SEDANG => '-10 s/d -20 Poin',
            self::BERAT => '-50 s/d -75 Poin',
            self::FATAL => '-100 Poin',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::RINGAN => 'bg-amber-100 text-amber-800 border border-amber-200',
            self::SEDANG => 'bg-orange-100 text-orange-800 border border-orange-200',
            self::BERAT => 'bg-rose-100 text-rose-800 border border-rose-300 font-medium',
            self::FATAL => 'bg-red-800 text-white font-bold',
        };
    }

    public function isRemediable(): bool
    {
        return match ($this) {
            self::RINGAN, self::SEDANG => true,
            self::BERAT, self::FATAL => false,
        };
    }

    public function isResetable(): bool
    {
        return match ($this) {
            self::RINGAN, self::SEDANG => true,
            self::BERAT, self::FATAL => false,
        };
    }
}

