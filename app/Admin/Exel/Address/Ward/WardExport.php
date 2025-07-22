<?php

namespace App\Admin\Exel\Address\Ward;

use App\Traits\ImportValidationTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WardExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use Importable, ImportValidationTrait;

    protected $wards;

    public function __construct($wards)
    {
        $this->wards = $wards;
    }

    public function collection(): Collection
    {
        if ($this->wards instanceof Collection) {
            return $this->wards;
        }

        return collect($this->wards);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Mã Tỉnh',
            'Tên Phường/Xã'
        ];
    }

    public function map($ward): array
    {
        return [
            $ward->id ?? '',
            $ward->province_id ?? '',
            $ward->name ?? ''
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);

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
