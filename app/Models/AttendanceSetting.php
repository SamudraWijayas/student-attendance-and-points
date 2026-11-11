<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_in_start',
        'on_time_limit',
        'late_start',
        'check_in_end',
        'tolerance_minutes',
        'active_days',
    ];

    protected function casts(): array
    {
        return [
            'active_days' => 'array',
            'tolerance_minutes' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'check_in_start' => '06:00:00',
                'on_time_limit' => '07:00:00',
                'late_start' => '07:00:01',
                'check_in_end' => '08:00:00',
                'tolerance_minutes' => 0,
                'active_days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ]
        );
    }
}

