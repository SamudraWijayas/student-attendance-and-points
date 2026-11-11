<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?int $classId = null
    ) {}

    public function collection()
    {
        $query = Attendance::with(['student.schoolClass.major']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('attendance_date', [$this->startDate, $this->endDate]);
        }

        if ($this->classId) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $this->classId));
        }

        return $query->orderBy('attendance_date', 'desc')->orderBy('check_in_time', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jurusan',
            'Jam Kedatangan',
            'Status Kehadiran',
            'Metode',
            'Catatan',
        ];
    }

    public function map($att): array
    {
        return [
            $att->attendance_date->format('d/m/Y'),
            $att->student->nis,
            $att->student->name,
            $att->student->schoolClass?->name,
            $att->student->schoolClass?->major?->name,
            substr($att->check_in_time, 0, 5) . ' WIB',
            $att->status->label(),
            $att->method->label(),
            $att->notes ?? '-',
        ];
    }
}

