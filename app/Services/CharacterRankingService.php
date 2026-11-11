<?php

namespace App\Services;

use App\Enums\PointType;
use App\Enums\StudentStatus;
use App\Enums\ViolationSeverity;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Support\Collection;

class CharacterRankingService
{
    /**
     * Compute and rank students for Juara Karakter with strict rule checking.
     */
    public function getJuaraKarakterRanking(
        ?int $semesterId = null,
        ?int $classId = null,
        ?int $majorId = null,
        ?int $enrollmentYear = null
    ): Collection {
        $activeSemester = $semesterId ? Semester::find($semesterId) : Semester::active();
        $targetSemesterId = $activeSemester?->id;

        $query = Student::where('status', StudentStatus::ACTIVE)
            ->with(['schoolClass.major', 'qrCode']);

        if ($classId) {
            $query->where('school_class_id', $classId);
        }

        if ($majorId) {
            $query->whereHas('schoolClass', fn($q) => $q->where('major_id', $majorId));
        }

        if ($enrollmentYear) {
            $query->where('enrollment_year', $enrollmentYear);
        }

        $students = $query->get();

        $ranked = $students->map(function (Student $student) use ($targetSemesterId) {
            // Get active transactions
            $transactionsQuery = $student->pointTransactions()->where('is_cancelled', false);
            if ($targetSemesterId) {
                $transactionsQuery->where('semester_id', $targetSemesterId);
            }
            $transactions = $transactionsQuery->get();

            $totalPrestasi = $transactions->where('type', PointType::PRESTASI)->sum('points');
            $totalPelanggaran = $transactions->where('type', PointType::PELANGGARAN)->sum('points');

            // Count violations by severity
            $minorCount = $transactions->where('severity', ViolationSeverity::RINGAN)->count();
            $moderateCount = $transactions->where('severity', ViolationSeverity::SEDANG)->count();
            $heavyCount = $transactions->where('severity', ViolationSeverity::BERAT)->count();
            $fatalCount = $transactions->where('severity', ViolationSeverity::FATAL)->count();

            // Strict rule: Wajib nol (0) pelanggaran kategori Sedang, Berat, maupun Fatal
            $disqualificationReasons = [];

            if ($moderateCount > 0) {
                $disqualificationReasons[] = "Memiliki {$moderateCount} pelanggaran kategori Sedang";
            }
            if ($heavyCount > 0) {
                $disqualificationReasons[] = "Memiliki {$heavyCount} pelanggaran kategori Berat";
            }
            if ($fatalCount > 0) {
                $disqualificationReasons[] = "Memiliki {$fatalCount} pelanggaran kategori Fatal";
            }

            $isEligible = empty($disqualificationReasons) && $totalPrestasi > 0;

            return [
                'student' => $student,
                'total_prestasi' => (int) $totalPrestasi,
                'total_pelanggaran' => (int) $totalPelanggaran,
                'net_points' => (int) ($totalPrestasi + $totalPelanggaran),
                'minor_count' => $minorCount,
                'moderate_count' => $moderateCount,
                'heavy_count' => $heavyCount,
                'fatal_count' => $fatalCount,
                'is_eligible' => $isEligible,
                'disqualification_reasons' => $disqualificationReasons,
            ];
        });

        // Sort: Eligible first, then descending by total_prestasi, then by net_points
        return $ranked->sort(function ($a, $b) {
            if ($a['is_eligible'] !== $b['is_eligible']) {
                return $b['is_eligible'] <=> $a['is_eligible'];
            }
            if ($a['total_prestasi'] !== $b['total_prestasi']) {
                return $b['total_prestasi'] <=> $a['total_prestasi'];
            }
            return $b['net_points'] <=> $a['net_points'];
        })->values()->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });
    }
}

