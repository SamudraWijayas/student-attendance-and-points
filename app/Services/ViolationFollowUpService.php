<?php

namespace App\Services;

use App\Enums\FollowUpStatus;
use App\Models\AuditLog;
use App\Models\ViolationFollowUp;
use Exception;
use Illuminate\Support\Facades\DB;

class ViolationFollowUpService
{
    /**
     * Update follow up progress and disciplinary records.
     *
     * @throws Exception
     */
    public function updateFollowUp(
        ViolationFollowUp $followUp,
        FollowUpStatus $status,
        ?string $followUpType = null,
        ?string $counselingNotes = null,
        ?string $statementLetterPath = null,
        ?string $warningLetterType = null,
        ?string $warningLetterPath = null,
        ?string $parentInvitationDate = null,
        ?string $parentInvitationStatus = null,
        ?string $resolutionNotes = null
    ): ViolationFollowUp {
        return DB::transaction(function () use (
            $followUp,
            $status,
            $followUpType,
            $counselingNotes,
            $statementLetterPath,
            $warningLetterType,
            $warningLetterPath,
            $parentInvitationDate,
            $parentInvitationStatus,
            $resolutionNotes
        ) {
            $data = [
                'status' => $status,
                'handled_by' => auth()->id() ?? 1,
            ];

            if ($followUpType) $data['follow_up_type'] = $followUpType;
            if ($counselingNotes !== null) $data['counseling_notes'] = $counselingNotes;
            if ($statementLetterPath !== null) $data['statement_letter_path'] = $statementLetterPath;
            if ($warningLetterType !== null) $data['warning_letter_type'] = $warningLetterType;
            if ($warningLetterPath !== null) $data['warning_letter_path'] = $warningLetterPath;
            if ($parentInvitationDate !== null) $data['parent_invitation_date'] = $parentInvitationDate;
            if ($parentInvitationStatus !== null) $data['parent_invitation_status'] = $parentInvitationStatus;
            if ($resolutionNotes !== null) $data['resolution_notes'] = $resolutionNotes;

            $followUp->update($data);

            AuditLog::log(
                action: 'UPDATE_FOLLOW_UP',
                module: 'disciplinary',
                recordId: $followUp->id,
                newValues: [
                    'student_id' => $followUp->student_id,
                    'status' => $status->value,
                    'warning_letter_type' => $warningLetterType,
                ]
            );

            return $followUp;
        });
    }
}

