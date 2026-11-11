<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\StudentStatus;
use App\Exports\StudentsTemplateExport;
use App\Imports\StudentsImport;
use App\Models\AuditLog;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\Student;
use App\Services\QRCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function __construct(
        protected QRCodeService $qrCodeService
    ) {}

    /**
     * Display students.
     */
    public function index(Request $request): View
    {
        $query = Student::with(['schoolClass.major', 'qrCode']);

        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }

        if ($request->filled('major_id')) {
            $query->whereHas('schoolClass', fn ($q) =>
                $q->where('major_id', $request->major_id)
            );
        }

        if ($request->filled('enrollment_year')) {
            $query->where('enrollment_year', $request->enrollment_year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        $classes = SchoolClass::where('is_active', true)->get();

        $majors = Major::where('is_active', true)->get();

        $enrollmentYears = Student::whereNotNull('enrollment_year')
            ->select('enrollment_year')
            ->distinct()
            ->orderByDesc('enrollment_year')
            ->pluck('enrollment_year');

        return view('students.index', [
            'students' => $students,
            'classes' => $classes,
            'majors' => $majors,
            'enrollmentYears' => $enrollmentYears,
        ]);
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        $classes = SchoolClass::where('is_active', true)
            ->with('major')
            ->get();

        return view('students.create', compact('classes'));
    }

    /**
     * Store student.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_class_id' => [
                'required',
                'exists:school_classes,id',
            ],
            'nis' => [
                'required',
                'string',
                'max:30',
                'unique:students,nis',
            ],
            'nisn' => [
                'nullable',
                'string',
                'max:30',
                'unique:students,nisn',
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'gender' => [
                'nullable',
                'in:L,P',
            ],
            'birth_place' => [
                'nullable',
                'string',
                'max:100',
            ],
            'birth_date' => [
                'nullable',
                'date',
            ],
            'address' => [
                'nullable',
                'string',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:25',
            ],
            'enrollment_year' => [
                'nullable',
                'integer',
                'min:2000',
                'max:2099',
            ],
            'status' => [
                'nullable',
                Rule::in([
                    'active',
                    'inactive',
                    'graduated',
                    'transferred',
                ]),
            ],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
        ], [
            'school_class_id.required' => 'Kelas wajib dipilih.',
            'school_class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
            'name.required' => 'Nama siswa wajib diisi.',
            'nisn.unique' => 'NISN sudah digunakan oleh siswa lain.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $validated['status'] ??= StudentStatus::ACTIVE->value;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')
                ->store('students/photos', 'public');
        }

        $student = Student::create($validated);

        $this->qrCodeService->generateForStudent($student);

        AuditLog::log(
            action: 'CREATE_STUDENT',
            module: 'students',
            recordId: $student->id,
            newValues: [
                'name' => $student->name,
                'nis' => $student->nis,
            ]
        );

        return redirect()
            ->route('students.show', $student->id)
            ->with(
                'success',
                "Data siswa {$student->name} dan QR Code absensi berhasil dibuat."
            );
    }

    /**
     * Show student detail.
     */
    public function show(Student $student): View
    {
        $student->load([
            'schoolClass.major',
            'qrCode',
            'attendances' => fn ($q) =>
                $q->latest('attendance_date')->take(20),
            'pointTransactions' => fn ($q) =>
                $q->with('category')
                    ->where('is_cancelled', false)
                    ->latest('incident_date'),
            'violationFollowUps' => fn ($q) =>
                $q->latest('follow_up_date'),
            'remedialPoints' => fn ($q) =>
                $q->with('pointTransaction')->latest('activity_date'),
            'snapshots' => fn ($q) =>
                $q->with(['academicYear', 'semester'])
                    ->latest('snapshot_date'),
        ]);

        $activeSemester = Semester::active();

        $pointsSummary = $student->getActivePointsSummary(
            $activeSemester?->id
        );

        $totalAttendance = $student->attendances()->count();

        $hadirCount = $student->attendances()
            ->where('status', AttendanceStatus::HADIR)
            ->count();

        $lateCount = $student->attendances()
            ->where('status', AttendanceStatus::TERLAMBAT)
            ->count();

        $attendancePercentage = $totalAttendance > 0
            ? round(($hadirCount / $totalAttendance) * 100, 1)
            : 0;

        $qrSvg = $student->qrCode
            ? $this->qrCodeService->getSvg($student->qrCode->token, 200)
            : null;

        $school = SchoolSetting::current();

        return view('students.show', [
            'student' => $student,
            'pointsSummary' => $pointsSummary,
            'totalAttendance' => $totalAttendance,
            'hadirCount' => $hadirCount,
            'lateCount' => $lateCount,
            'attendancePercentage' => $attendancePercentage,
            'qrSvg' => $qrSvg,
            'school' => $school,
        ]);
    }

    /**
     * Show edit form.
     */
    public function edit(Student $student): View
    {
        $classes = SchoolClass::where('is_active', true)
            ->with('major')
            ->get();

        return view('students.edit', compact('student', 'classes'));
    }

    /**
     * Update student.
     */
    public function update(
        Request $request,
        Student $student
    ): RedirectResponse {
        $validated = $request->validate([
            'school_class_id' => [
                'required',
                'exists:school_classes,id',
            ],
            'nis' => [
                'required',
                'string',
                'max:30',
                Rule::unique('students', 'nis')->ignore($student->id),
            ],
            'nisn' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('students', 'nisn')->ignore($student->id),
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'gender' => [
                'nullable',
                'in:L,P',
            ],
            'birth_place' => [
                'nullable',
                'string',
                'max:100',
            ],
            'birth_date' => [
                'nullable',
                'date',
            ],
            'address' => [
                'nullable',
                'string',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:25',
            ],
            'enrollment_year' => [
                'nullable',
                'integer',
                'min:2000',
                'max:2099',
            ],
            'status' => [
                'nullable',
                Rule::in([
                    'active',
                    'inactive',
                    'graduated',
                    'transferred',
                ]),
            ],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
        ], [
            'school_class_id.required' => 'Kelas wajib dipilih.',
            'school_class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
            'name.required' => 'Nama siswa wajib diisi.',
            'nisn.unique' => 'NISN sudah digunakan oleh siswa lain.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($request->hasFile('photo')) {
            if (
                $student->photo &&
                Storage::disk('public')->exists($student->photo)
            ) {
                Storage::disk('public')->delete($student->photo);
            }

            $validated['photo'] = $request->file('photo')
                ->store('students/photos', 'public');
        }

        $student->update($validated);

        AuditLog::log(
            action: 'UPDATE_STUDENT',
            module: 'students',
            recordId: $student->id,
            newValues: [
                'name' => $student->name,
                'nis' => $student->nis,
            ]
        );

        return redirect()
            ->route('students.show', $student->id)
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Delete student (soft delete).
     */
    public function destroy(Student $student): RedirectResponse
    {
        $hasHistory = $student->attendances()->exists()
            || $student->pointTransactions()->exists();

        $studentName = $student->name;

        $student->delete();

        AuditLog::log(
            action: 'DELETE_STUDENT',
            module: 'students',
            recordId: $student->id,
            newValues: [
                'name' => $studentName,
                'had_history' => $hasHistory,
            ]
        );

        return redirect()
            ->route('students.index')
            ->with(
                'success',
                "Data siswa {$studentName} berhasil dihapus/diarsipkan."
            );
    }

    /**
     * Regenerate QR Code.
     */
    public function regenerateQr(Student $student): RedirectResponse
    {
        $this->qrCodeService->generateForStudent(
            $student,
            forceRegenerate: true
        );

        AuditLog::log(
            action: 'REGENERATE_QR',
            module: 'students',
            recordId: $student->id
        );

        return back()->with(
            'success',
            'QR Code siswa berhasil diregenerasi. Kode lama telah dinonaktifkan.'
        );
    }

    /**
     * Print single student card.
     */
    public function printCard(Student $student): View
    {
        $student->load(['schoolClass.major', 'qrCode']);

        $qrSvg = $student->qrCode
            ? $this->qrCodeService->getSvg($student->qrCode->token, 200)
            : null;

        $school = SchoolSetting::current();

        return view('students.print-card', [
            'student' => $student,
            'qrSvg' => $qrSvg,
            'school' => $school,
        ]);
    }

    /**
     * Print multiple student cards by class.
     */
    public function printCardsByClass(SchoolClass $schoolClass): View
    {
        $students = $schoolClass->students()
            ->where('status', StudentStatus::ACTIVE)
            ->with('qrCode')
            ->get();

        $school = SchoolSetting::current();

        $cardsData = $students->map(function ($student) {
            return [
                'student' => $student,
                'qrSvg' => $student->qrCode
                    ? $this->qrCodeService->getSvg(
                        $student->qrCode->token,
                        180
                    )
                    : null,
            ];
        });

        return view('students.print-multiple-cards', [
            'schoolClass' => $schoolClass,
            'cardsData' => $cardsData,
            'school' => $school,
        ]);
    }

    // ==========================================
    // IMPORT EXCEL
    // ==========================================

    /**
     * Show student import page.
     */
    public function importIndex(): View
    {
        return view('students.import');
    }

    /**
     * Download Excel import template.
     */
    public function downloadImportTemplate()
    {
        return Excel::download(
            new StudentsTemplateExport(),
            'template-import-siswa.xlsx'
        );
    }

        /**
         * Import students from Excel.
         */
        public function importExcel(Request $request): RedirectResponse
        {
            $request->validate([
                'file' => [
                    'required',
                    'file',
                    'mimes:xlsx,xls,csv',
                    'max:10240',
                ],
            ], [
                'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
                'file.mimes' => 'Format file harus XLSX, XLS, atau CSV.',
                'file.max' => 'Ukuran file maksimal 10MB.',
            ]);

            try {
                $studentsImport = new StudentsImport(
                    $this->qrCodeService
                );

                Excel::import(
                    $studentsImport,
                    $request->file('file')
                );

                $result = $studentsImport->result();

                AuditLog::log(
                    action: 'IMPORT_STUDENTS',
                    module: 'students',
                    newValues: [
                        'success' => $result['success'],
                        'skipped' => $result['skipped'],
                        'failed' => $result['failed'],
                    ]
                );

                return redirect()
                    ->route('students.import.index')
                    ->with('import_result', $result);

            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error(
                    'IMPORT EXCEL SISWA GAGAL',
                    [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                    ]
                );

                return redirect()
                    ->route('students.import.index')
                    ->with(
                        'error',
                        config('app.debug')
                            ? 'Gagal import: ' . $e->getMessage()
                            : 'Gagal membaca file Excel. Periksa format file.'
                    );
            }
        }
}
