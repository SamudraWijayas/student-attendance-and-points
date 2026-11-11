<?php

namespace App\Enums;

enum FollowUpStatus: string
{
    case BELUM_DITANGANI = 'belum_ditangani';
    case DALAM_PROSES = 'dalam_proses';
    case SELESAI = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::BELUM_DITANGANI => 'Belum Ditangani',
            self::DALAM_PROSES => 'Dalam Proses',
            self::SELESAI => 'Selesai',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::BELUM_DITANGANI => 'bg-rose-100 text-rose-800 border border-rose-200',
            self::DALAM_PROSES => 'bg-amber-100 text-amber-800 border border-amber-200',
            self::SELESAI => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
        };
    }
}

