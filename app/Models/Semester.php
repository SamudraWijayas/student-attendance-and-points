<?php

namespace App\Models;

use App\Enums\SemesterType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'semester_type',
        'is_active',
        'is_reset_completed',
        'reset_at',
        'reset_by',
    ];

    protected function casts(): array
    {
        return [
            'semester_type' => SemesterType::class,
            'is_active' => 'boolean',
            'is_reset_completed' => 'boolean',
            'reset_at' => 'datetime',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function resetByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reset_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(SemesterPointSnapshot::class);
    }

    public static function active(): ?self
    {
        return static::with('academicYear')->where('is_active', true)->first();
    }
}

