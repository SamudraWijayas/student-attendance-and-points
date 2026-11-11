<?php

namespace App\Enums;

enum PointType: string
{
    case PRESTASI = 'prestasi';
    case PELANGGARAN = 'pelanggaran';

    public function label(): string
    {
        return match ($this) {
            self::PRESTASI => 'Prestasi (+)',
            self::PELANGGARAN => 'Pelanggaran (-)',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PRESTASI => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            self::PELANGGARAN => 'bg-rose-100 text-rose-800 border border-rose-200',
        };
    }
}

