<?php

namespace App\Imports;

use App\Enums\StudentStatus;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\QRCodeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public int $success = 0;

    public int $skipped = 0;

    public int $failed = 0;

    public array $errors = [];

    public function __construct(
        protected QRCodeService $qrCodeService
    ) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            // Baris pertama adalah heading.
            $rowNumber = $index + 2;

            try {
                // Pastikan data berbentuk Collection.
                $data = $row instanceof Collection
                    ? $row
                    : collect($row);

                $nis = trim((string) ($data->get('nis') ?? ''));
                $name = trim((string) ($data->get('nama') ?? ''));
                $className = trim((string) ($data->get('kelas') ?? ''));

                // Lewati baris kosong.
                if ($nis === '' && $name === '' && $className === '') {
                    continue;
                }

                // Validasi data wajib.
                if ($nis === '' || $name === '' || $className === '') {
                    $this->addError(
                        $rowNumber,
                        $nis,
                        'NIS, nama, dan kelas wajib diisi.'
                    );

                    continue;
                }

                // Validasi panjang karakter.
                if (strlen($nis) > 30 || mb_strlen($name) > 150) {
                    $this->addError(
                        $rowNumber,
                        $nis,
                        'NIS maksimal 30 karakter dan nama maksimal 150 karakter.'
                    );

                    continue;
                }

                // Periksa NIS yang sudah terdaftar.
                if (Student::where('nis', $nis)->exists()) {
                    $this->skipped++;

                    $this->addError(
                        $rowNumber,
                        $nis,
                        'NIS sudah terdaftar. Data dilewati.'
                    );

                    continue;
                }

                // Cari kelas berdasarkan nama.
                $classes = SchoolClass::where('name', $className)->get();

                if ($classes->isEmpty()) {
                    $this->addError(
                        $rowNumber,
                        $nis,
                        "Kelas '{$className}' tidak ditemukan."
                    );

                    continue;
                }

                if ($classes->count() > 1) {
                    $this->addError(
                        $rowNumber,
                        $nis,
                        "Nama kelas '{$className}' ditemukan lebih dari satu. Tidak dapat menentukan kelas."
                    );

                    continue;
                }

                $schoolClass = $classes->first();

                // Simpan siswa dan QR Code dalam satu transaksi.
                DB::transaction(function () use ($schoolClass, $nis, $name) {
                    $student = Student::create([
                        'school_class_id' => $schoolClass->id,
                        'nis' => $nis,
                        'name' => $name,
                        'status' => StudentStatus::ACTIVE->value,
                    ]);

                    $this->qrCodeService->generateForStudent($student);
                });

                $this->success++;

            } catch (\Throwable $e) {
                Log::error('Import siswa gagal.', [
                    'row' => $rowNumber,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                $this->addError(
                    $rowNumber,
                    $nis ?? '-',
                    $e->getMessage()
                );
            }
        }
    }

    private function addError(
        int $row,
        string $nis,
        string $message
    ): void {
        $this->failed++;

        $this->errors[] = [
            'row' => $row,
            'nis' => $nis !== '' ? $nis : '-',
            'message' => $message,
        ];
    }

    public function result(): array
    {
        return [
            'success' => $this->success,
            'skipped' => $this->skipped,
            'failed' => $this->failed,
            'errors' => $this->errors,
        ];
    }
}
