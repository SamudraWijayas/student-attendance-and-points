<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SemesterPointSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'semester_id',
        'total_achievement_points',
        'total_minor_violations',
        'total_moderate_violations',
        'total_heavy_violations',
        'total_fatal_violations',
        'active_sp_level',
        'snapshot_date',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'total_achievement_points' => 'integer',
            'total_minor_violations' => 'integer',
            'total_moderate_violations' => 'integer',
            'total_heavy_violations' => 'integer',
            'total_fatal_violations' => 'integer',
            'snapshot_date' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function processedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}

