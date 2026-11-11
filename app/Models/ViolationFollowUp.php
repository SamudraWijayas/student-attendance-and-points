<?php

namespace App\Models;

use App\Enums\FollowUpStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViolationFollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'point_transaction_id',
        'follow_up_type',
        'follow_up_date',
        'status',
        'counseling_notes',
        'statement_letter_path',
        'warning_letter_type',
        'warning_letter_path',
        'parent_invitation_date',
        'parent_invitation_status',
        'resolution_notes',
        'handled_by',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'parent_invitation_date' => 'date',
            'status' => FollowUpStatus::class,
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

    public function handledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}

