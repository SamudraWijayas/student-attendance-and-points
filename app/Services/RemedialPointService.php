<?php

namespace App\Services;

use App\Enums\ViolationSeverity;
use App\Models\AuditLog;
use App\Models\PointTransaction;
use App\Models\RemedialPoint;
use Exception;
use Illuminate\Support\Facades\DB;

class RemedialPointService
{
    /**
     * Record a remedial activity to reduce minus points.
     *
     * @throws Exception
     */
    public function recordRemedial(
        int $pointTransactionId,
        string $activityType,
        string $activityDate,
        int $pointsDeducted,
        string $description,
        ?string $verificationNotes = null
    ): RemedialPoint {
        $transaction = PointTransaction::with('category')->findOrFail($pointTransactionId);

        if ($transaction->is_cancelled) {
            throw new Exception("Pelanggaran ini sudah dibatalkan dan tidak memerlukan remedial.");
        }

        // Strict rule: only Ringan and Sedang are remediable
        $severity = $transaction->severity;
        if ($severity === ViolationSeverity::BERAT || $severity === ViolationSeverity::FATAL) {
            throw new Exception("Kebijakan Sekolah: Pelanggaran kategori {$severity->label()} TIDAK DAPAT diremedial.");
        }

        if ($pointsDeducted <= 0) {
            throw new Exception("Poin pemutihan remedial harus bernilai positif.");
        }

        // Check remaining violation points
        $remaining = $transaction->remaining_violation_points;
        if ($pointsDeducted > $remaining) {
            throw new Exception("Poin remedial ({$pointsDeducted}) melebihi sisa poin pelanggaran kasus ini ({$remaining} poin tersisa).");
        }

        return DB::transaction(function () use (
            $transaction,
            $activityType,
            $activityDate,
            $pointsDeducted,
            $description,
            $verificationNotes
        ) {
            $remedial = RemedialPoint::create([
                'student_id' => $transaction->student_id,
                'point_transaction_id' => $transaction->id,
                'activity_type' => $activityType,
                'activity_date' => $activityDate,
                'points_deducted' => $pointsDeducted,
                'description' => $description,
                'verification_notes' => $verificationNotes,
                'recorded_by' => auth()->id() ?? 1,
            ]);

            AuditLog::log(
                action: 'REMEDIAL',
                module: 'points',
                recordId: $remedial->id,
                newValues: [
                    'student_id' => $transaction->student_id,
                    'transaction_id' => $transaction->id,
                    'points_deducted' => $pointsDeducted,
                    'activity' => $activityType,
                ]
            );

            return $remedial;
        });
    }
}

