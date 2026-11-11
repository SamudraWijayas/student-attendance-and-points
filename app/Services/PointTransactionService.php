<?php

namespace App\Services;

use App\Enums\FollowUpStatus;
use App\Enums\PointType;
use App\Enums\StudentStatus;
use App\Models\AuditLog;
use App\Models\PointCategory;
use App\Models\PointTransaction;
use App\Models\Semester;
use App\Models\Student;
use App\Models\ViolationFollowUp;
use Exception;
use Illuminate\Support\Facades\DB;

class PointTransactionService
{
    /**
     * Store new point transaction (Prestasi or Pelanggaran).
     *
     * @throws Exception
     */
    public function recordTransaction(
        int $studentId,
        int $categoryId,
        int $points,
        string $incidentDate,
        string $description,
        ?string $evidencePath = null,
        ?string $initialFollowUpType = null
    ): PointTransaction {
        $student = Student::findOrFail($studentId);
        if ($student->status !== StudentStatus::ACTIVE) {
            throw new Exception("Siswa {$student->name} tidak berstatus aktif.");
        }

        $category = PointCategory::findOrFail($categoryId);
        if (!$category->is_active) {
            throw new Exception("Kategori poin '{$category->action_name}' sedang dinonaktifkan.");
        }

        // Validate points sign and range
        if ($category->type === PointType::PRESTASI) {
            if ($points <= 0) {
                throw new Exception("Poin prestasi harus bernilai positif.");
            }
            if ($points < $category->min_points || $points > $category->max_points) {
                throw new Exception("Nilai poin prestasi harus berada di antara {$category->min_points} dan {$category->max_points}.");
            }
            $finalPoints = $points;
        } else {
            // Pelanggaran: ensure stored points are negative
            $absPoints = abs($points);
            $minAbs = abs($category->min_points);
            $maxAbs = abs($category->max_points);

            if ($absPoints < min($minAbs, $maxAbs) || $absPoints > max($minAbs, $maxAbs)) {
                $rangeMin = min($minAbs, $maxAbs);
                $rangeMax = max($minAbs, $maxAbs);
                throw new Exception("Nilai poin pelanggaran harus berada di rentang -{$rangeMax} s/d -{$rangeMin} poin.");
            }
            $finalPoints = -1 * $absPoints;
        }

        $activeSemester = Semester::active();

        return DB::transaction(function () use (
            $student,
            $category,
            $finalPoints,
            $incidentDate,
            $description,
            $evidencePath,
            $activeSemester,
            $initialFollowUpType
        ) {
            $transaction = PointTransaction::create([
                'student_id' => $student->id,
                'point_category_id' => $category->id,
                'academic_year_id' => $activeSemester?->academic_year_id ?? $student->schoolClass?->academic_year_id,
                'semester_id' => $activeSemester?->id,
                'type' => $category->type,
                'severity' => $category->severity,
                'points' => $finalPoints,
                'incident_date' => $incidentDate,
                'description' => $description,
                'evidence_path' => $evidencePath,
                'recorded_by' => auth()->id() ?? 1,
            ]);

            // If violation, automatically generate follow-up case record
            if ($category->type === PointType::PELANGGARAN) {
                $defaultFollowUpType = $initialFollowUpType ?? match ($category->severity?->value) {
                    'ringan' => 'Teguran Lisan & Penataan Seragam',
                    'sedang' => 'Konseling & Surat Pernyataan',
                    'berat' => 'Panggilan Orang Tua & SP 1',
                    'fatal' => 'Konferensi Kasus & Sidang Pleno',
                    default => 'Pencatatan Kedisiplinan',
                };

                ViolationFollowUp::create([
                    'student_id' => $student->id,
                    'point_transaction_id' => $transaction->id,
                    'follow_up_type' => $defaultFollowUpType,
                    'follow_up_date' => $incidentDate,
                    'status' => FollowUpStatus::BELUM_DITANGANI,
                    'handled_by' => auth()->id() ?? 1,
                ]);
            }

            AuditLog::log(
                action: 'STORE_POINT',
                module: 'points',
                recordId: $transaction->id,
                newValues: [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'type' => $category->type->value,
                    'points' => $finalPoints,
                    'action_name' => $category->action_name,
                ]
            );

            return $transaction;
        });
    }

    /**
     * Cancel a point transaction non-destructively with mandatory reason.
     *
     * @throws Exception
     */
    public function cancelTransaction(PointTransaction $transaction, string $reason): PointTransaction
    {
        if ($transaction->is_cancelled) {
            throw new Exception("Transaksi ini sudah pernah dibatalkan sebelumnya.");
        }

        if (empty(trim($reason))) {
            throw new Exception("Alasan pembatalan transaksi wajib diisi.");
        }

        $transaction->update([
            'is_cancelled' => true,
            'cancellation_reason' => trim($reason),
            'cancelled_by' => auth()->id() ?? 1,
            'cancelled_at' => now(),
        ]);

        AuditLog::log(
            action: 'CANCEL_POINT',
            module: 'points',
            recordId: $transaction->id,
            newValues: [
                'cancellation_reason' => $reason,
                'cancelled_by' => auth()->id(),
            ]
        );

        return $transaction;
    }
}

