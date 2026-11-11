<?php

namespace App\Enums;

enum SemesterType: string
{
    case GANJIL = 'ganjil';
    case GENAP = 'genap';

    public function label(): string
    {
        return match ($this) {
            self::GANJIL => 'Semester Ganjil',
            self::GENAP => 'Semester Genap',
        };
    }
}

