<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\PointType;
use App\Enums\StudentStatus;
use App\Models\Attendance;
use App\Models\PointTransaction;
use App\Models\RemedialPoint;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\ViolationFollowUp;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function getDailyAttendance(string $date, ?int $classId = null): Collection
    {
        $query = Attendance::where('attendance_date', $date)
            ->with(['student.schoolClass.major', 'recordedByUser']);

        if ($classId) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $classId));
        }

        return $query->orderBy('check_in_time', 'asc')->get();
    }

    public function getAbsentStudents(string $date, ?int $classId = null): Collection
    {
        $attendedStudentIds = Attendance::where('attendance_date', $date)->pluck('student_id');

        $query = Student::where('status', StudentStatus::ACTIVE)
            ->whereNotIn('id', $attendedStudentIds)
            ->with(['schoolClass.major']);

        if ($classId) {
            $query->where('school_class_id', $classId);
        }

        return $query->orderBy('name', 'asc')->get();
    }

    public function getMonthlyAttendanceSummary(int $month, int $year, ?int $classId = null): Collection
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $studentsQuery = Student::where('status', StudentStatus::ACTIVE)->with(['schoolClass.major']);
        if ($classId) {
            $studentsQuery->where('school_class_id', $classId);
        }
        $students = $studentsQuery->get();

        $attendances = Attendance::whereBetween('attendance_date', [$startDate, $endDate])->get();

        return $students->map(function ($student) use ($attendances) {
            $records = $attendances->where('student_id', $student->id);
            $hadir = $records->where('status', AttendanceStatus::HADIR)->count();
            $terlambat = $records->where('status', AttendanceStatus::TERLAMBAT)->count();
            return [
                'student' => $student,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'total_hadir' => $hadir + $terlambat,
            ];
        });
    }

    public function getPointTransactions(
        ?string $type = null,
        ?string $severity = null,
        ?int $classId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        $query = PointTransaction::where('is_cancelled', false)
            ->with(['student.schoolClass.major', 'category', 'recordedByUser']);

        if ($type) {
            $query->where('type', $type);
        }
        if ($severity) {
            $query->where('severity', $severity);
        }
        if ($classId) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $classId));
        }
        if ($startDate && $endDate) {
            $query->whereBetween('incident_date', [$startDate, $endDate]);
        }

        return $query->orderBy('incident_date', 'desc')->get();
    }

    public function getDisciplinaryBook(?int $studentId = null, ?int $classId = null): Collection
    {
        $query = Student::with([
            'schoolClass.major',
            'pointTransactions.category',
            'violationFollowUps',
            'remedialPoints',
        ]);

        if ($studentId) {
            $query->where('id', $studentId);
        }
        if ($classId) {
            $query->where('school_class_id', $classId);
        }

        return $query->get();
    }

    /**
     * Generate PDF stream/download with official school identity.
     */
    public function generatePdf(string $view, array $data, string $paper = 'a4', string $orientation = 'portrait')
    {
        $school = SchoolSetting::current();
        $data['school'] = $school;

        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper($paper, $orientation);

        return $pdf;
    }
}

