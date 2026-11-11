<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\StudentStatus;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\AttendanceService;
use App\Services\ReportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
        protected ReportService $reportService
    ) {}

    /**
     * Display live QR Code scanner page.
     */
    public function scan(): View
    {
        $settings = AttendanceSetting::current();
        $todayStats = $this->attendanceService->getTodayStats();
        $recentScans = Attendance::where('attendance_date', Carbon::now('Asia/Jakarta')->toDateString())
            ->with(['student.schoolClass.major'])
            ->latest('check_in_time')
            ->take(10)
            ->get();

        return view('attendance.scan', [
            'settings' => $settings,
            'todayStats' => $todayStats,
            'recentScans' => $recentScans,
        ]);
    }

    /**
     * Process QR Code scan via AJAX.
     */
    public function processScan(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        try {
            $result = $this->attendanceService->recordQrScan($request->token);
            $todayStats = $this->attendanceService->getTodayStats();

            return response()->json([
                'success' => true,
                'status' => $result['status']->value,
                'status_label' => $result['status']->label(),
                'time' => substr($result['time'], 0, 5),
                'message' => $result['message'],
                'student' => [
                    'id' => $result['student']->id,
                    'name' => $result['student']->name,
                    'nis' => $result['student']->nis,
                    'class' => $result['student']->schoolClass?->name,
                    'major' => $result['student']->schoolClass?->major?->name,
                    'photo' => $result['student']->photo,
                ],
                'stats' => $todayStats,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display daily attendance monitoring page.
     */
    public function monitoring(Request $request): View
    {
        $date = $request->input('date', Carbon::now('Asia/Jakarta')->toDateString());
        $classId = $request->input('class_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Attendance::where('attendance_date', $date)
            ->with(['student.schoolClass.major', 'recordedByUser']);

        if ($classId) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $classId));
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderBy('check_in_time', 'desc')->paginate(20)->withQueryString();

        $classes = SchoolClass::where('is_active', true)->get();
        $majors = Major::where('is_active', true)->get();
        $todayStats = $this->attendanceService->getTodayStats();

        // Students who have not attended yet today
        $absentStudents = $this->reportService->getAbsentStudents($date, $classId);

        return view('attendance.monitoring', [
            'attendances' => $attendances,
            'classes' => $classes,
            'majors' => $majors,
            'todayStats' => $todayStats,
            'currentDate' => $date,
            'selectedClass' => $classId,
            'selectedStatus' => $status,
            'search' => $search,
            'absentStudents' => $absentStudents,
        ]);
    }

    /**
     * Display overall attendance history page.
     */
    public function history(Request $request): View
    {
        $query = Attendance::with(['student.schoolClass.major', 'recordedByUser']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('attendance_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('month') && $request->filled('year')) {
            $query->whereYear('attendance_date', $request->year)
                  ->whereMonth('attendance_date', $request->month);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $request->class_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"));
        }

        $attendances = $query->orderBy('attendance_date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate(25)
            ->withQueryString();

        $classes = SchoolClass::where('is_active', true)->get();

        return view('attendance.history', [
            'attendances' => $attendances,
            'classes' => $classes,
        ]);
    }

    /**
     * Store manual attendance.
     */
    public function storeManual(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'attendance_date' => ['required', 'date'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'status' => ['required', 'in:hadir,terlambat'],
            'notes' => ['required', 'string', 'max:255'],
        ], [
            'notes.required' => 'Alasan pencatatan manual wajib diisi.',
        ]);

        try {
            $this->attendanceService->recordManual(
                studentId: (int) $request->student_id,
                date: $request->attendance_date,
                time: $request->check_in_time . ':00',
                status: AttendanceStatus::from($request->status),
                reason: $request->notes
            );

            return back()->with('success', 'Absensi manual berhasil dicatat.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Correct an existing attendance record.
     */
    public function correct(Request $request, Attendance $attendance): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:hadir,terlambat'],
            'reason' => ['required', 'string', 'max:255'],
        ], [
            'reason.required' => 'Alasan koreksi status kehadiran wajib diisi.',
        ]);

        try {
            $this->attendanceService->correctAttendance(
                $attendance,
                AttendanceStatus::from($request->status),
                $request->reason
            );

            return back()->with('success', 'Koreksi data absensi berhasil disimpan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * View and update attendance settings.
     */
    public function settings(): View
    {
        $settings = AttendanceSetting::current();
        return view('attendance.settings', compact('settings'));
    }

    /**
     * Save attendance settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'check_in_start' => ['required'],
            'on_time_limit' => ['required'],
            'check_in_end' => ['required'],
            'tolerance_minutes' => ['required', 'integer', 'min:0'],
            'active_days' => ['required', 'array', 'min:1'],
        ]);

        $settings = AttendanceSetting::current();
        $settings->update([
            'check_in_start' => $request->check_in_start,
            'on_time_limit' => $request->on_time_limit,
            'late_start' => Carbon::parse($request->on_time_limit)->addSecond()->toTimeString(),
            'check_in_end' => $request->check_in_end,
            'tolerance_minutes' => (int) $request->tolerance_minutes,
            'active_days' => $request->active_days,
        ]);

        return back()->with('success', 'Pengaturan jam absensi berhasil disimpan.');
    }
}

