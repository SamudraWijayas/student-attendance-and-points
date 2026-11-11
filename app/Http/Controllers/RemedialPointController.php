<?php

namespace App\Http\Controllers;

use App\Enums\PointType;
use App\Enums\ViolationSeverity;
use App\Models\PointTransaction;
use App\Models\RemedialPoint;
use App\Models\Student;
use App\Services\RemedialPointService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RemedialPointController extends Controller
{
    public function __construct(
        protected RemedialPointService $remedialPointService
    ) {}

    public function index(Request $request): View
    {
        $remedials = RemedialPoint::with([
            'student.schoolClass',
            'pointTransaction.category',
            'recordedByUser',
        ])->orderBy('activity_date', 'desc')->paginate(15);

        // Fetch eligible violations that still have remaining points
        $eligibleViolations = PointTransaction::where('type', PointType::PELANGGARAN)
            ->whereIn('severity', [ViolationSeverity::RINGAN, ViolationSeverity::SEDANG])
            ->where('is_cancelled', false)
            ->with(['student.schoolClass', 'category', 'remedials'])
            ->get()
            ->filter(fn($tx) => $tx->remaining_violation_points > 0);

        return view('remedials.index', compact('remedials', 'eligibleViolations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'point_transaction_id' => ['required', 'exists:point_transactions,id'],
            'activity_type' => ['required', 'string', 'max:150'],
            'activity_date' => ['required', 'date'],
            'points_deducted' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'verification_notes' => ['nullable', 'string'],
        ], [
            'activity_type.required' => 'Pilih jenis kegiatan remedial (Kerja Bakti Sosial / Tugas Amal Sholeh).',
            'points_deducted.min' => 'Nilai poin remedial minimal 1 poin.',
        ]);

        try {
            $this->remedialPointService->recordRemedial(
                pointTransactionId: (int) $request->point_transaction_id,
                activityType: $request->activity_type,
                activityDate: $request->activity_date,
                pointsDeducted: (int) $request->points_deducted,
                description: $request->description,
                verificationNotes: $request->verification_notes
            );

            return back()->with('success', 'Catatan remedial berhasil disimpan dan mengurangi poin minus siswa.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}

