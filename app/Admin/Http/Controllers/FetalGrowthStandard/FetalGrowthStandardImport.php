<?php

namespace App\Admin\Http\Controllers\FetalGrowthStandard;

use App\Enums\ActiveStatus;
use App\Models\FetalGrowthStandard;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class FetalGrowthStandardImport implements ToCollection
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            // Bỏ qua dòng tiêu đề
            if ($index === 0) continue;

            $week = isset($row[0]) ? (int) $row[0] : null;
            if (!$week || $week < 1 || $week > 50) continue;

            $length = isset($row[1]) ? (float) $row[1] : null;
            $weight = isset($row[2]) ? (float) $row[2] : null;
            $head = isset($row[3]) && $row[3] !== '' ? (float) $row[3] : null;

            if ($length === null && $weight === null) continue;

            FetalGrowthStandard::updateOrCreate(
                ['week' => $week],
                [
                    'length' => $length ?? 0,
                    'weight' => $weight ?? 0,
                    'head_circumference' => $head,
                    'status' => ActiveStatus::Active->value,
                ]
            );
        }
    }
}
