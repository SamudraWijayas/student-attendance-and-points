<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * Data contoh pada template.
     */
    public function array(): array
    {
        return [
            [
                '2026001',
                'Ahmad Fauzi',
                'X TKJ 1',
            ],
            [
                '2026002',
                'Siti Aminah',
                'X TKJ 1',
            ],
        ];
    }

    /**
     * Header kolom Excel.
     */
    public function headings(): array
    {
        return [
            'nis',
            'nama',
            'kelas',
        ];
    }

    /**
     * Styling header.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'rgb' => '025964',
                    ],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ];
    }
}
