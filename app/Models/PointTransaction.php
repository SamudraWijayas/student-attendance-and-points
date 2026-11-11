<?php

namespace App\Models;

use App\Enums\PointType;
use App\Enums\ViolationSeverity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'point_category_id',
        'academic_year_id',
        'semester_id',
        'type',
        'severity',
        'points',
        'incident_date',
        'description',
        'evidence_path',
        'recorded_by',
        'is_cancelled',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => PointType::class,
            'severity' => ViolationSeverity::class,
            'points' => 'integer',
            'incident_date' => 'date',
            'is_cancelled' => 'boolean',
            'cancelled_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PointCategory::class, 'point_category_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function recordedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function followUp(): HasOne
    {
        return $this->hasOne(ViolationFollowUp::class);
    }

    public function remedials(): HasMany
    {
        return $this->hasMany(RemedialPoint::class);
    }

    // Remaining points available for remedial
    public function getRemainingViolationPointsAttribute(): int
    {
        if ($this->type !== PointType::PELANGGARAN || $this->is_cancelled) {
            return 0;
        }

        $totalRemedial = $this->remedials()->sum('points_deducted');
        // abs($this->points) is total minus incurred, e.g. 10
        $remaining = abs($this->points) - $totalRemedial;
        return max(0, $remaining);
    }
}

