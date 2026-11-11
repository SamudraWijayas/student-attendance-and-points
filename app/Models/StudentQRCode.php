<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentQRCode extends Model
{
    use HasFactory;

    protected $table = 'student_qr_codes';

    protected $fillable = [
        'student_id',
        'token',
        'qr_image_path',
        'is_active',
        'generated_at',
        'deactivated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'generated_at' => 'datetime',
            'deactivated_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

