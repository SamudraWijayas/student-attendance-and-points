<?php

namespace App\Enums;

enum StudentStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case GRADUATED = 'graduated';
    case TRANSFERRED = 'transferred';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Tidak Aktif',
            self::GRADUATED => 'Lulus',
            self::TRANSFERRED => 'Pindah',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            self::INACTIVE => 'bg-slate-100 text-slate-800 border border-slate-300',
            self::GRADUATED => 'bg-blue-100 text-blue-800 border border-blue-200',
            self::TRANSFERRED => 'bg-purple-100 text-purple-800 border border-purple-200',
        };
    }
}

