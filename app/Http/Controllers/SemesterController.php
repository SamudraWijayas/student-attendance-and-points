<?php

namespace App\Http\Controllers;

use App\Enums\SemesterType;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Semester;
use App\Services\SemesterResetService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SemesterController extends Controller
{
    public function __construct(
        protected SemesterResetService $semesterResetService
    ) {}

    public function index(): View
    {
        $semesters = Semester::with(['academicYear', 'resetByUser'])->orderBy('id', 'desc')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('semesters.index', compact('semesters', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_type' => ['required', 'in:ganjil,genap'],
            'is_active' => ['boolean'],
        ]);

        if ($request->boolean('is_active')) {
            Semester::where('is_active', true)->update(['is_active' => false]);
        }

        $semester = Semester::firstOrCreate(
            [
                'academic_year_id' => $request->academic_year_id,
                'semester_type' => SemesterType::from($request->semester_type),
            ],
            [
                'is_active' => $request->boolean('is_active'),
            ]
        );

        AuditLog::log('CREATE_SEMESTER', 'semesters', $semester->id);

        return back()->with('success', 'Semester berhasil ditambahkan.');
    }

    public function setActive(Semester $semester): RedirectResponse
    {
        Semester::where('is_active', true)->update(['is_active' => false]);
        $semester->update(['is_active' => true]);

        // Also activate the related academic year
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $semester->academicYear()->update(['is_active' => true]);

        AuditLog::log('ACTIVATE_SEMESTER', 'semesters', $semester->id);

        return back()->with('success', "Semester {$semester->semester_type->label()} {$semester->academicYear?->name} sekarang berstatus AKTIF.");
    }

    /**
     * Execute semester reset (snapshot, reset prestasi & ringan/sedang, preserve berat/fatal/SP).
     */
    public function executeReset(Request $request, Semester $semester): RedirectResponse
    {
        $request->validate([
            'confirm_text' => ['required', 'in:RESET SEMESTER'],
        ], [
            'confirm_text.in' => 'Ketik tulisan "RESET SEMESTER" untuk mengonfirmasi tindakan ini.',
        ]);

        try {
            $result = $this->semesterResetService->executeReset($semester);
            return back()->with('success', $result['message']);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

