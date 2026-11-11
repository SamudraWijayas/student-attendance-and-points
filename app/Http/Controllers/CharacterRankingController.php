<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\Student;
use App\Services\CharacterRankingService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CharacterRankingController extends Controller
{
    public function __construct(
        protected CharacterRankingService $rankingService,
        protected ReportService $reportService
    ) {}

    public function index(Request $request): View
    {
        $semesterId = $request->input('semester_id');
        $classId = $request->input('class_id');
        $majorId = $request->input('major_id');
        $enrollmentYear = $request->input('enrollment_year');

        $activeSemester = $semesterId ? Semester::find($semesterId) : Semester::active();

        $rankings = $this->rankingService->getJuaraKarakterRanking(
            semesterId: $activeSemester?->id,
            classId: $classId,
            majorId: $majorId,
            enrollmentYear: $enrollmentYear
        );

        $semesters = Semester::with('academicYear')->orderBy('id', 'desc')->get();
        $classes = SchoolClass::where('is_active', true)->get();
        $majors = Major::where('is_active', true)->get();
        $enrollmentYears = Student::select('enrollment_year')->distinct()->orderBy('enrollment_year', 'desc')->pluck('enrollment_year');

        return view('points.juara-karakter', [
            'rankings' => $rankings,
            'activeSemester' => $activeSemester,
            'semesters' => $semesters,
            'classes' => $classes,
            'majors' => $majors,
            'enrollmentYears' => $enrollmentYears,
            'selectedSemester' => $semesterId,
            'selectedClass' => $classId,
            'selectedMajor' => $majorId,
            'selectedYear' => $enrollmentYear,
        ]);
    }

    /**
     * Print official Juara Karakter Certificate.
     */
    public function printCertificate(Student $student): View
    {
        $school = SchoolSetting::current();
        $activeSemester = Semester::active();
        $student->load(['schoolClass.major']);
        $points = $student->getActivePointsSummary($activeSemester?->id);

        return view('points.print-certificate', [
            'student' => $student,
            'school' => $school,
            'semester' => $activeSemester,
            'points' => $points,
        ]);
    }

    /**
     * Buku Induk Kedisiplinan Siswa.
     */
    public function disciplinaryBook(Request $request): View
    {
        $classId = $request->input('class_id');
        $search = $request->input('search');

        $query = Student::where('status', \App\Enums\StudentStatus::ACTIVE)
            ->with([
                'schoolClass.major',
                'pointTransactions' => fn($q) => $q->where('is_cancelled', false),
                'violationFollowUps',
                'remedialPoints',
                'snapshots',
            ]);

        if ($classId) {
            $query->where('school_class_id', $classId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(15)->withQueryString();
        $classes = SchoolClass::where('is_active', true)->get();

        return view('points.disciplinary-book', compact('students', 'classes', 'classId', 'search'));
    }
}

