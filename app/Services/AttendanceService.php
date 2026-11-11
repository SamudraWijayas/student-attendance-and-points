<?php

namespace App\Services;

use App\Enums\AttendanceMethod;
use App\Enums\AttendanceStatus;
use App\Enums\StudentStatus;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\AuditLog;
use App\Models\Semester;
use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(
        protected QRCodeService $qrCodeService
    ) {}

    /**
     * Process QR code scan attendance.
     *
     * @throws Exception
     */
    public function recordQrScan(string $token): array
    {
        $token = trim($token);
        if (empty($token)) {
            throw new Exception('Token QR Code tidak valid atau kosong.');
        }

        $student = $this->qrCodeService->findStudentByToken($token);

        if (!$student) {
            throw new Exception('Kartu QR Code tidak terdaftar atau sudah dinonaktifkan.');
        }

        if ($student->status !== StudentStatus::ACTIVE) {
            throw new Exception("Siswa {$student->name} berstatus {$student->status->label()} dan tidak dapat melakukan absensi.");
        }

        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        $settings = AttendanceSetting::current();

        // Determine if within check-in window
        $checkInStart = $settings->check_in_start;
        $checkInEnd = $settings->check_in_end;

        if ($currentTime < $checkInStart) {
            throw new Exception("Waktu absensi belum dibuka. Jam mulai absensi: {$checkInStart} WIB.");
        }

        if ($currentTime > $checkInEnd) {
            throw new Exception("Waktu absensi hari ini telah ditutup pada jam {$checkInEnd} WIB.");
        }

        // Determine status
        $onTimeLimit = $settings->on_time_limit;
        $status = ($currentTime <= $onTimeLimit) ? AttendanceStatus::HADIR : AttendanceStatus::TERLAMBAT;

        $activeSemester = Semester::active();

        // Atomic transaction with row lock
        $attendance = DB::transaction(function () use ($student, $today, $currentTime, $status, $activeSemester) {
            // Check if already checked in today
            $existing = Attendance::where('student_id', $student->id)
                ->where('attendance_date', $today)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new Exception("Siswa {$student->name} ({$student->nis}) sudah melakukan absensi hari ini pada pukul {$existing->check_in_time} WIB.");
            }

            return Attendance::create([
                'student_id' => $student->id,
                'academic_year_id' => $activeSemester?->academic_year_id ?? $student->schoolClass?->academic_year_id,
                'semester_id' => $activeSemester?->id,
                'attendance_date' => $today,
                'check_in_time' => $currentTime,
                'status' => $status,
                'method' => AttendanceMethod::QR_CODE,
                'recorded_by' => auth()->id(),
            ]);
        });

        AuditLog::log(
            action: 'SCAN_ATTENDANCE',
            module: 'attendance',
            recordId: $attendance->id,
            newValues: [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'nis' => $student->nis,
                'status' => $status->value,
                'time' => $currentTime,
            ]
        );

        return [
            'success' => true,
            'attendance' => $attendance,
            'student' => $student,
            'status' => $status,
            'time' => $currentTime,
            'message' => $status === AttendanceStatus::HADIR
                ? "Absensi Berhasil: {$student->name} (Hadir Tepat Waktu)"
                : "Absensi Terlambat: {$student->name} tercatat pukul {$currentTime} WIB",
        ];
    }

    /**
     * Process manual attendance recording by Admin.
     *
     * @throws Exception
     */
    public function recordManual(int $studentId, string $date, string $time, AttendanceStatus $status, string $reason): Attendance
    {
        $student = Student::findOrFail($studentId);

        if ($student->status !== StudentStatus::ACTIVE) {
            throw new Exception("Siswa {$student->name} berstatus tidak aktif.");
        }

        $activeSemester = Semester::active();

        $attendance = DB::transaction(function () use ($student, $date, $time, $status, $reason, $activeSemester) {
            $existing = Attendance::where('student_id', $student->id)
                ->where('attendance_date', $date)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new Exception("Siswa sudah memiliki catatan absensi pada tanggal {$date}. Silakan gunakan fitur koreksi jika ingin mengubah.");
            }

            return Attendance::create([
                'student_id' => $student->id,
                'academic_year_id' => $activeSemester?->academic_year_id ?? $student->schoolClass?->academic_year_id,
                'semester_id' => $activeSemester?->id,
                'attendance_date' => $date,
                'check_in_time' => $time,
                'status' => $status,
                'method' => AttendanceMethod::MANUAL,
                'recorded_by' => auth()->id(),
                'notes' => $reason,
            ]);
        });

        AuditLog::log(
            action: 'MANUAL_ATTENDANCE',
            module: 'attendance',
            recordId: $attendance->id,
            newValues: [
                'student_id' => $student->id,
                'date' => $date,
                'time' => $time,
                'status' => $status->value,
                'reason' => $reason,
            ]
        );

        return $attendance;
    }

    /**
     * Correction of an existing attendance record with reason.
     */
    public function correctAttendance(Attendance $attendance, AttendanceStatus $newStatus, string $reason): Attendance
    {
        $oldValues = [
            'status' => $attendance->status->value,
            'notes' => $attendance->notes,
        ];

        $attendance->update([
            'status' => $newStatus,
            'notes' => ($attendance->notes ? $attendance->notes . ' | ' : '') . 'Koreksi: ' . $reason . ' (Oleh Admin ID: ' . auth()->id() . ')',
        ]);

        AuditLog::log(
            action: 'CORRECT_ATTENDANCE',
            module: 'attendance',
            recordId: $attendance->id,
            newValues: ['status' => $newStatus->value, 'reason' => $reason],
            oldValues: $oldValues
        );

        return $attendance;
    }

    /**
     * Today's summary statistics.
     */
    public function getTodayStats(): array
    {
        $today = Carbon::now('Asia/Jakarta')->toDateString();

        $totalActiveStudents = Student::where('status', StudentStatus::ACTIVE)->count();

        $todayAttendances = Attendance::where('attendance_date', $today)->get();

        $hadir = $todayAttendances->where('status', AttendanceStatus::HADIR)->count();
        $terlambat = $todayAttendances->where('status', AttendanceStatus::TERLAMBAT)->count();
        $totalRecorded = $hadir + $terlambat;
        $belumHadir = max(0, $totalActiveStudents - $totalRecorded);

        $percentage = $totalActiveStudents > 0
            ? round(($totalRecorded / $totalActiveStudents) * 100, 1)
            : 0;

        return [
            'total_active' => $totalActiveStudents,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'total_recorded' => $totalRecorded,
            'belum_hadir' => $belumHadir,
            'percentage' => $percentage,
            'today_date' => $today,
        ];
    }
}

