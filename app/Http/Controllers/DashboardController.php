<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\PointType;
use App\Enums\StudentStatus;
use App\Enums\ViolationSeverity;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Major;
use App\Models\PointTransaction;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Services\AttendanceService;
use App\Services\CharacterRankingService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
        protected CharacterRankingService $rankingService
    ) {}

    public function index(): View
    {
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $activeSemester = Semester::active();

        // 1. General counts
        $totalActiveStudents = Student::where('status', StudentStatus::ACTIVE)->count();
        $totalClasses = SchoolClass::where('is_active', true)->count();
        $totalMajors = Major::where('is_active', true)->count();

        // 2. Attendance Stats Today
        $attendanceStats = $this->attendanceService->getTodayStats();

        // 3. Point Stats for Active Semester
        $pointsQuery = PointTransaction::where('is_cancelled', false);
        if ($activeSemester) {
            $pointsQuery->where('semester_id', $activeSemester->id);
        }
        $transactions = $pointsQuery->get();

        $totalPrestasiPoints = $transactions->where('type', PointType::PRESTASI)->sum('points');
        $totalPelanggaranPoints = abs($transactions->where('type', PointType::PELANGGARAN)->sum('points'));

        // 4. Last 7 Days Attendance Trend (for Chart.js)
        $sevenDaysLabels = [];
        $sevenDaysHadir = [];
        $sevenDaysTerlambat = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now('Asia/Jakarta')->subDays($i);
            $dStr = $d->toDateString();
            $sevenDaysLabels[] = $d->translatedFormat('D, d M');

            $records = Attendance::where('attendance_date', $dStr)->get();
            $sevenDaysHadir[] = $records->where('status', AttendanceStatus::HADIR)->count();
            $sevenDaysTerlambat[] = $records->where('status', AttendanceStatus::TERLAMBAT)->count();
        }

        // 5. Attendance distribution by class today
        $classes = SchoolClass::withCount(['students' => fn($q) => $q->where('status', StudentStatus::ACTIVE)])
            ->where('is_active', true)
            ->get();

        $classLabels = [];
        $classPresentCounts = [];

        foreach ($classes as $cls) {
            $classLabels[] = $cls->name;
            $present = Attendance::where('attendance_date', $today)
                ->whereHas('student', fn($q) => $q->where('school_class_id', $cls->id))
                ->count();
            $classPresentCounts[] = $present;
        }

        // 6. Recent Activity Feeds
        $recentAttendances = Attendance::where('attendance_date', $today)
            ->with(['student.schoolClass.major'])
            ->latest('check_in_time')
            ->take(5)
            ->get();

        $todayLateStudents = Attendance::where('attendance_date', $today)
            ->where('status', AttendanceStatus::TERLAMBAT)
            ->with(['student.schoolClass'])
            ->latest('check_in_time')
            ->take(5)
            ->get();

        $recentPrestasi = PointTransaction::where('type', PointType::PRESTASI)
            ->where('is_cancelled', false)
            ->with(['student.schoolClass', 'category'])
            ->latest('incident_date')
            ->take(4)
            ->get();

        $recentPelanggaran = PointTransaction::where('type', PointType::PELANGGARAN)
            ->where('is_cancelled', false)
            ->with(['student.schoolClass', 'category'])
            ->latest('incident_date')
            ->take(4)
            ->get();

        // 7. Top 5 Students Ranking
        $topStudents = $this->rankingService->getJuaraKarakterRanking($activeSemester?->id)->take(5);

        return view('dashboard.index', [
            'totalActiveStudents' => $totalActiveStudents,
            'totalClasses' => $totalClasses,
            'totalMajors' => $totalMajors,
            'attendanceStats' => $attendanceStats,
            'totalPrestasiPoints' => $totalPrestasiPoints,
            'totalPelanggaranPoints' => $totalPelanggaranPoints,
            'sevenDaysLabels' => $sevenDaysLabels,
            'sevenDaysHadir' => $sevenDaysHadir,
            'sevenDaysTerlambat' => $sevenDaysTerlambat,
            'classLabels' => $classLabels,
            'classPresentCounts' => $classPresentCounts,
            'recentAttendances' => $recentAttendances,
            'todayLateStudents' => $todayLateStudents,
            'recentPrestasi' => $recentPrestasi,
            'recentPelanggaran' => $recentPelanggaran,
            'topStudents' => $topStudents,
            'activeSemester' => $activeSemester,
        ]);
    }
}

