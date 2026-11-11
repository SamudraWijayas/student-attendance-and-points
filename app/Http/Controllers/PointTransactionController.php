<?php

namespace App\Http\Controllers;

use App\Enums\PointType;
use App\Enums\StudentStatus;
use App\Enums\ViolationSeverity;
use App\Models\PointCategory;
use App\Models\PointTransaction;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\PointTransactionService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointTransactionController extends Controller
{
    public function __construct(
        protected PointTransactionService $pointTransactionService
    ) {}

    /**
     * Show form to input point transaction.
     */
    public function create(Request $request): View
    {
        $selectedStudentId = $request->input('student_id');
        $preselectedType = $request->input('type', 'prestasi');

        $students = Student::where('status', StudentStatus::ACTIVE)->with('schoolClass')->orderBy('name')->get();
        $categories = PointCategory::where('is_active', true)->orderBy('type')->orderBy('min_points')->get();

        return view('points.create', [
            'students' => $students,
            'categories' => $categories,
            'selectedStudentId' => $selectedStudentId,
            'preselectedType' => $preselectedType,
        ]);
    }

    /**
     * Store point transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'point_category_id' => ['required', 'exists:point_categories,id'],
            'points' => ['required', 'integer'],
            'incident_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:1000'],
            'evidence' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'follow_up_type' => ['nullable', 'string', 'max:100'],
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('evidence/points', 'public');
        }

        try {
            $transaction = $this->pointTransactionService->recordTransaction(
                studentId: (int) $request->student_id,
                categoryId: (int) $request->point_category_id,
                points: (int) $request->points,
                incidentDate: $request->incident_date,
                description: $request->description,
                evidencePath: $evidencePath,
                initialFollowUpType: $request->follow_up_type
            );

            return redirect()->route('students.show', $transaction->student_id)
                ->with('success', 'Transaksi poin berhasil dicatat ke dalam sistem.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show list of Prestasi.
     */
    public function prestasi(Request $request): View
    {
        $query = PointTransaction::where('type', PointType::PRESTASI)
            ->where('is_cancelled', false)
            ->with(['student.schoolClass.major', 'category', 'recordedByUser']);

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $request->class_id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', fn($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"))
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $transactions = $query->orderBy('incident_date', 'desc')->paginate(20)->withQueryString();
        $classes = SchoolClass::where('is_active', true)->get();

        return view('points.prestasi', compact('transactions', 'classes'));
    }

    /**
     * Show list of Pelanggaran.
     */
    public function pelanggaran(Request $request): View
    {
        $query = PointTransaction::where('type', PointType::PELANGGARAN)
            ->where('is_cancelled', false)
            ->with(['student.schoolClass.major', 'category', 'recordedByUser', 'followUp', 'remedials']);

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $request->class_id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', fn($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"))
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $transactions = $query->orderBy('incident_date', 'desc')->paginate(20)->withQueryString();
        $classes = SchoolClass::where('is_active', true)->get();

        return view('points.pelanggaran', compact('transactions', 'classes'));
    }

    /**
     * Show overall transaction history with cancel action.
     */
    public function history(Request $request): View
    {
        $query = PointTransaction::with(['student.schoolClass', 'category', 'recordedByUser', 'cancelledByUser']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"));
        }

        $transactions = $query->orderBy('incident_date', 'desc')->paginate(20)->withQueryString();

        return view('points.history', compact('transactions'));
    }

    /**
     * Cancel a point transaction non-destructively.
     */
    public function cancel(Request $request, PointTransaction $pointTransaction): RedirectResponse
    {
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:255'],
        ], [
            'cancellation_reason.required' => 'Alasan pembatalan transaksi poin wajib diisi.',
        ]);

        try {
            $this->pointTransactionService->cancelTransaction($pointTransaction, $request->cancellation_reason);
            return back()->with('success', 'Transaksi poin berhasil dibatalkan. Riwayat pembatalan tersimpan dalam audit.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

