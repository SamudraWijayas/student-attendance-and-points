<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentQRCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    /**
     * Generate or regenerate a unique active QR Code token for a student.
     */
    public function generateForStudent(Student $student, bool $forceRegenerate = false): StudentQRCode
    {
        return DB::transaction(function () use ($student, $forceRegenerate) {
            $existing = $student->qrCode;

            if ($existing && !$forceRegenerate && $existing->is_active) {
                return $existing;
            }

            // Deactivate existing codes if any
            StudentQRCode::where('student_id', $student->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'deactivated_at' => now(),
                ]);

            // Generate an unguessable unique token (UUID + random string)
            do {
                $token = 'SMKTS-' . (string) Str::uuid() . '-' . Str::random(8);
            } while (StudentQRCode::where('token', $token)->exists());

            return StudentQRCode::create([
                'student_id' => $student->id,
                'token' => $token,
                'is_active' => true,
                'generated_at' => now(),
            ]);
        });
    }

    /**
     * Generate SVG string of the QR Code directly for rendering in Blade.
     */
    public function getSvg(string $token, int $size = 200): string
    {
        return QrCode::size($size)
            ->format('svg')
            ->margin(1)
            ->generate($token);
    }

    /**
     * Resolve active student from QR code token.
     */
    public function findStudentByToken(string $token): ?Student
    {
        $qr = StudentQRCode::where('token', trim($token))
            ->where('is_active', true)
            ->with(['student.schoolClass.major'])
            ->first();

        return $qr?->student;
    }
}

