<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Exports\CharacterPointsExport;
use App\Models\Attendance;
use App\Models\PointTransaction;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Laporan Absensi View.
     */
    public function attendance(Request $request): View
    {
        $date = $request->input('date', Carbon::now('Asia/Jakarta')->toDateString());
        $classId = $request->input('class_id');

        $attendances = $this->reportService->getDailyAttendance($date, $classId);
        $absentStudents = $this->reportService->getAbsentStudents($date, $classId);
        $classes = SchoolClass::where('is_active', true)->get();

        return view('reports.attendance', compact('attendances', 'absentStudents', 'classes', 'date', 'classId'));
    }

    /**
     * Export PDF Laporan Absensi Harian.
     */
    public function attendancePdf(Request $request)
    {
        $date = $request->input('date', Carbon::now('Asia/Jakarta')->toDateString());
        $classId = $request->input('class_id');

        $attendances = $this->reportService->getDailyAttendance($date, $classId);
        $school = SchoolSetting::current();
        $selectedClass = $classId ? SchoolClass::find($classId) : null;

        $pdf = Pdf::loadView('reports.pdf-attendance', [
            'attendances' => $attendances,
            'school' => $school,
            'date' => $date,
            'selectedClass' => $selectedClass,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("Laporan_Absensi_SMKTS_{$date}.pdf");
    }

    /**
     * Export Excel Laporan Absensi.
     */
    public function attendanceExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now('Asia/Jakarta')->toDateString());
        $endDate = $request->input('end_date', Carbon::now('Asia/Jakarta')->toDateString());
        $classId = $request->input('class_id');

        return Excel::download(
            new AttendanceExport($startDate, $endDate, $classId),
            "Rekap_Absensi_SMKTS_{$startDate}_{$endDate}.xlsx"
        );
    }

    /**
     * Laporan Poin Karakter View.
     */
    public function points(Request $request): View
    {
        $type = $request->input('type');
        $severity = $request->input('severity');
        $classId = $request->input('class_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transactions = $this->reportService->getPointTransactions($type, $severity, $classId, $startDate, $endDate);
        $classes = SchoolClass::where('is_active', true)->get();

        return view('reports.points', compact('transactions', 'classes', 'type', 'severity', 'classId', 'startDate', 'endDate'));
    }

    /**
     * Export PDF Laporan Poin Karakter.
     */
    public function pointsPdf(Request $request)
    {
        $type = $request->input('type');
        $severity = $request->input('severity');
        $classId = $request->input('class_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transactions = $this->reportService->getPointTransactions($type, $severity, $classId, $startDate, $endDate);
        $school = SchoolSetting::current();

        $pdf = Pdf::loadView('reports.pdf-points', [
            'transactions' => $transactions,
            'school' => $school,
            'type' => $type,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("Laporan_Poin_Karakter_SMKTS.pdf");
    }

    /**
     * Export Excel Laporan Poin Karakter.
     */
    public function pointsExcel(Request $request)
    {
        $type = $request->input('type');
        $classId = $request->input('class_id');

        return Excel::download(
            new CharacterPointsExport($type, $classId),
            "Rekap_Poin_Karakter_SMKTS.xlsx"
        );
    }
}

