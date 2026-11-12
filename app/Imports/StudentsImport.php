<?php

namespace App\Imports;

use App\Enums\StudentStatus;
use App\Models\SchoolClass;
use App\Models\Student;
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

    /**
     * Import students from Excel collection.
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            // Baris pertama adalah heading.
            $rowNumber = $index + 2;

            $nis = '-';

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
                        'NIS sudah terdaftar. Data dilewati.',
                        countAsFailed: false
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

                // Simpan siswa dalam transaksi.
                DB::transaction(function () use (
                    $schoolClass,
                    $nis,
                    $name
                ) {
                    Student::create([
                        'school_class_id' => $schoolClass->id,
                        'nis' => $nis,
                        'name' => $name,
                        'status' => StudentStatus::ACTIVE->value,
                    ]);
                });

                $this->success++;

            } catch (\Throwable $e) {
                Log::error('Import siswa gagal.', [
                    'row' => $rowNumber,
                    'nis' => $nis,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                $this->addError(
                    $rowNumber,
                    $nis,
                    $e->getMessage()
                );
            }
        }
    }

    /**
     * Add import error.
     */
    private function addError(
        int $row,
        string $nis,
        string $message,
        bool $countAsFailed = true
    ): void {
        if ($countAsFailed) {
            $this->failed++;
        }

        $this->errors[] = [
            'row' => $row,
            'nis' => $nis !== '' ? $nis : '-',
            'message' => $message,
        ];
    }

    /**
     * Get import result.
     */
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
