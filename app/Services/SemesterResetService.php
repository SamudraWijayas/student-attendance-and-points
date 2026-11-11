<?php

namespace App\Services;

use App\Enums\PointType;
use App\Enums\ViolationSeverity;
use App\Models\AuditLog;
use App\Models\PointTransaction;
use App\Models\Semester;
use App\Models\SemesterPointSnapshot;
use App\Models\Student;
use App\Models\ViolationFollowUp;
use Exception;
use Illuminate\Support\Facades\DB;

class SemesterResetService
{
    /**
     * Execute semester point reset with snapshot archiving.
     *
     * @throws Exception
     */
    public function executeReset(Semester $semester): array
    {
        if ($semester->is_reset_completed) {
            throw new Exception("Semester '{$semester->academicYear?->name} - {$semester->semester_type->label()}' sudah pernah direset pada {$semester->reset_at->format('d/m/Y H:i')} WIB.");
        }

        return DB::transaction(function () use ($semester) {
            $students = Student::with(['pointTransactions' => function ($q) use ($semester) {
                $q->where('semester_id', $semester->id)->where('is_cancelled', false);
            }])->get();

            $snapshotCount = 0;

            foreach ($students as $student) {
                $txs = $student->pointTransactions;

                $prestasi = $txs->where('type', PointType::PRESTASI)->sum('points');
                $minor = $txs->where('severity', ViolationSeverity::RINGAN)->sum('points');
                $moderate = $txs->where('severity', ViolationSeverity::SEDANG)->sum('points');
                $heavy = $txs->where('severity', ViolationSeverity::BERAT)->sum('points');
                $fatal = $txs->where('severity', ViolationSeverity::FATAL)->sum('points');

                // Determine active SP level if any
                $latestSp = ViolationFollowUp::where('student_id', $student->id)
                    ->whereNotNull('warning_letter_type')
                    ->latest('follow_up_date')
                    ->first();

                SemesterPointSnapshot::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'semester_id' => $semester->id,
                    ],
                    [
                        'academic_year_id' => $semester->academic_year_id,
                        'total_achievement_points' => $prestasi,
                        'total_minor_violations' => abs($minor),
                        'total_moderate_violations' => abs($moderate),
                        'total_heavy_violations' => abs($heavy),
                        'total_fatal_violations' => abs($fatal),
                        'active_sp_level' => $latestSp?->warning_letter_type,
                        'snapshot_date' => now(),
                        'processed_by' => auth()->id() ?? 1,
                    ]
                );

                $snapshotCount++;
            }

            // Mark semester reset as complete
            $semester->update([
                'is_reset_completed' => true,
                'reset_at' => now(),
                'reset_by' => auth()->id() ?? 1,
            ]);

            AuditLog::log(
                action: 'SEMESTER_RESET',
                module: 'semester',
                recordId: $semester->id,
                newValues: [
                    'semester_id' => $semester->id,
                    'snapshots_created' => $snapshotCount,
                    'reset_at' => now()->toDateTimeString(),
                ]
            );

            return [
                'success' => true,
                'snapshots_count' => $snapshotCount,
                'semester' => $semester,
                'message' => "Reset semester berhasil. Snapshot {$snapshotCount} siswa telah diarsipkan. Poin pelanggaran berat, fatal, dan rekam jejak SP tetap terpelihara.",
            ];
        });
    }
}

