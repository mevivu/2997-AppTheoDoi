<?php

namespace App\Admin\Exel\Address\Province;

use App\Traits\ImportValidationTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProvinceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use Importable, ImportValidationTrait;

    protected $provinces;

    public function __construct($provinces)
    {
        $this->provinces = $provinces;
    }

    public function collection(): Collection
    {
        return $this->provinces;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên tỉnh'
        ];
    }

    public function map($province): array
    {
        return [
            $province->id,
            $province->name ?? ''
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFE2E2E2'
                    ]
                ]
            ]
        ];
    }
}
