<?php

namespace App\Models;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_class_id',
        'nis',
        'nisn',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'phone',
        'photo',
        'enrollment_year',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'enrollment_year' => 'integer',
            'status' => StudentStatus::class,
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function qrCode(): HasOne
    {
        return $this->hasOne(StudentQRCode::class)->where('is_active', true);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(StudentQRCode::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function violationFollowUps(): HasMany
    {
        return $this->hasMany(ViolationFollowUp::class);
    }

    public function remedialPoints(): HasMany
    {
        return $this->hasMany(RemedialPoint::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(SemesterPointSnapshot::class);
    }

    public function getActivePointsSummary(?int $semesterId = null): array
    {
        $query = $this->pointTransactions()->where('is_cancelled', false);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $transactions = $query->get();

        $prestasi = $transactions->where('type', 'prestasi')->sum('points');
        $pelanggaran = $transactions->where('type', 'pelanggaran')->sum('points');

        $remedialQuery = $this->remedialPoints();

        if ($semesterId) {
            $remedialQuery->whereHas(
                'pointTransaction',
                fn ($q) => $q->where('semester_id', $semesterId)
            );
        }

        $remedialDeducted = $remedialQuery->sum('points_deducted');

        $effectivePelanggaran = min(0, $pelanggaran + $remedialDeducted);

        return [
            'prestasi' => (int) $prestasi,
            'pelanggaran' => (int) $pelanggaran,
            'remedial' => (int) $remedialDeducted,
            'effective_pelanggaran' => (int) $effectivePelanggaran,
            'total_net' => (int) ($prestasi + $effectivePelanggaran),
            'has_moderate' => $transactions->where('severity', 'sedang')->isNotEmpty(),
            'has_heavy' => $transactions->where('severity', 'berat')->isNotEmpty(),
            'has_fatal' => $transactions->where('severity', 'fatal')->isNotEmpty(),
        ];
    }

    public function isActive(): bool
    {
        return $this->status === StudentStatus::ACTIVE;
    }
}