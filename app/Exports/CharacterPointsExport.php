<?php

namespace App\Exports;

use App\Models\PointTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CharacterPointsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected ?string $type = null,
        protected ?int $classId = null
    ) {}

    public function collection()
    {
        $query = PointTransaction::where('is_cancelled', false)
            ->with(['student.schoolClass.major', 'category']);

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->classId) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $this->classId));
        }

        return $query->orderBy('incident_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Kejadian',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jenis Transaksi',
            'Tingkat Keparahan',
            'Kategori Tindakan',
            'Nilai Poin',
            'Kronologi / Keterangan',
        ];
    }

    public function map($tx): array
    {
        return [
            $tx->incident_date->format('d/m/Y'),
            $tx->student->nis,
            $tx->student->name,
            $tx->student->schoolClass?->name,
            strtoupper($tx->type->value),
            $tx->severity ? $tx->severity->label() : '-',
            $tx->category?->action_name,
            $tx->points,
            $tx->description,
        ];
    }
}

