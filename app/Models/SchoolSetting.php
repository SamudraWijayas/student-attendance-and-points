<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'school_logo',
        'address',
        'phone',
        'email',
        'website',
        'principal_name',
        'principal_nip',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'school_name' => 'SMK Tri Sukses Natar',
                'address' => 'Jl. Branti Raya No. 1, Natar, Kabupaten Lampung Selatan, Lampung',
                'phone' => '(0721) 123456',
                'email' => 'info@smktrisuksesnatar.sch.id',
                'website' => 'https://smktrisuksesnatar.sch.id',
                'principal_name' => 'Drs. H. Ahmad Sudrajat, M.Pd.',
                'principal_nip' => '197505122000031005',
            ]
        );
    }
}

