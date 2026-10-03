<?php

namespace App\Admin\Exel\FetalGrowthStandard;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FetalGrowthStandardTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection(): Collection
    {
        return collect([
            [8, 1.6, 1.0, ''],
            [9, 2.3, 2.0, ''],
            [10, 3.1, 4.0, ''],
            [11, 4.1, 45.0, ''],
            [12, 5.4, 58.0, ''],
            [13, 7.4, 73.0, ''],
            [14, 8.7, 93.0, ''],
            [15, 10.1, 117.0, ''],
            [16, 11.6, 146.0, ''],
            [17, 13.0, 181.0, ''],
            [18, 14.2, 222.0, ''],
            [19, 15.3, 272.0, ''],
            [20, 25.6, 330.0, ''],
            [21, 26.7, 400.0, ''],
            [22, 27.8, 476.0, ''],
            [23, 28.9, 565.0, ''],
            [24, 30.0, 665.0, ''],
            [25, 34.6, 756.0, ''],
            [26, 35.6, 900.0, ''],
            [27, 36.6, 1000.0, ''],
            [28, 37.6, 1100.0, ''],
            [29, 38.6, 1239.0, ''],
            [30, 39.9, 1396.0, ''],
            [31, 41.1, 1568.0, ''],
            [32, 42.4, 1755.0, ''],
            [33, 43.7, 2000.0, ''],
            [34, 45.0, 2200.0, ''],
            [35, 46.2, 2378.0, ''],
            [36, 47.4, 2600.0, ''],
            [37, 48.6, 2800.0, ''],
            [38, 49.8, 3000.0, ''],
            [39, 50.7, 3186.0, ''],
            [40, 51.2, 3338.0, ''],
            [41, 51.7, 3600.0, ''],
            [42, 51.7, 3700.0, ''],
        ]);
    }

    public function headings(): array
    {
        return [
            'Tuần (số nguyên)',
            'Chiều dài chuẩn (cm)',
            'Cân nặng chuẩn (g)',
            'Chu vi đầu (cm - tùy chọn)',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 25,
            'D' => 28,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->setShowGridlines(true);
        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Header styling
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'name' => 'Segoe UI',
                'bold' => true,
                'size' => 11,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F7A80'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $totalRows = 36; // 1 header + 35 data rows (tuần 8-42)

        for ($row = 2; $row <= $totalRows; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(21);

            // Alternating subtle zebra striping
            $fillColor = ($row % 2 === 0) ? 'FFFFFFFF' : 'FFF8FAFC';
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                'font' => [
                    'name' => 'Segoe UI',
                    'size' => 10,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $fillColor],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Cột A: Tuần thai căn giữa, in đậm
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);

            // Cột B: Chiều dài căn phải, định dạng 1 chữ số thập phân
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0.0');

            // Cột C: Cân nặng căn phải, định dạng số có dấu phẩy phân cách
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('#,##0.0');

            // Cột D: Chu vi đầu căn giữa
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0.0');
        }

        // Đường viền mảnh cho toàn bộ bảng
        $sheet->getStyle("A1:D{$totalRows}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCBD5E1'],
                ],
            ],
        ]);

        return [];
    }
}
