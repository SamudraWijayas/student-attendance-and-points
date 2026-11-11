<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemedialPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'point_transaction_id',
        'activity_type',
        'activity_date',
        'points_deducted',
        'description',
        'verification_notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'points_deducted' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function pointTransaction(): BelongsTo
    {
        return $this->belongsTo(PointTransaction::class);
    }

    public function recordedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}

