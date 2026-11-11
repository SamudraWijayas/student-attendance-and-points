<?php

namespace App\Http\Controllers;

use App\Enums\FollowUpStatus;
use App\Models\ViolationFollowUp;
use App\Services\ViolationFollowUpService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ViolationFollowUpController extends Controller
{
    public function __construct(
        protected ViolationFollowUpService $followUpService
    ) {}

    public function index(Request $request): View
    {
        $query = ViolationFollowUp::with([
            'student.schoolClass.major',
            'pointTransaction.category',
            'handledByUser',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('has_sp')) {
            $query->whereNotNull('warning_letter_type');
        }

        $followUps = $query->orderBy('follow_up_date', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'belum_ditangani' => ViolationFollowUp::where('status', FollowUpStatus::BELUM_DITANGANI)->count(),
            'dalam_proses' => ViolationFollowUp::where('status', FollowUpStatus::DALAM_PROSES)->count(),
            'selesai' => ViolationFollowUp::where('status', FollowUpStatus::SELESAI)->count(),
            'total_sp' => ViolationFollowUp::whereNotNull('warning_letter_type')->count(),
        ];

        return view('follow-ups.index', compact('followUps', 'stats'));
    }

    public function update(Request $request, ViolationFollowUp $violationFollowUp): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:belum_ditangani,dalam_proses,selesai'],
            'follow_up_type' => ['nullable', 'string', 'max:100'],
            'counseling_notes' => ['nullable', 'string'],
            'warning_letter_type' => ['nullable', 'in:sp1,sp2,sp3'],
            'parent_invitation_date' => ['nullable', 'date'],
            'parent_invitation_status' => ['nullable', 'in:dikirim,hadir,tidak_hadir'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        try {
            $this->followUpService->updateFollowUp(
                followUp: $violationFollowUp,
                status: FollowUpStatus::from($request->status),
                followUpType: $request->follow_up_type,
                counselingNotes: $request->counseling_notes,
                warningLetterType: $request->warning_letter_type,
                parentInvitationDate: $request->parent_invitation_date,
                parentInvitationStatus: $request->parent_invitation_status,
                resolutionNotes: $request->resolution_notes
            );

            return back()->with('success', 'Catatan tindak lanjut kedisiplinan berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

