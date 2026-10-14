<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Models\IqNorm;
use Illuminate\Database\Seeder;

class IqNormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baselineData = [
            1 => ['mean' => 104.58, 'sd' => 35.43, 'sample' => 48, 'min' => 30, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            2 => ['mean' => 116.00, 'sd' => 28.41, 'sample' => 70, 'min' => 20, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            3 => ['mean' => 111.22, 'sd' => 33.03, 'sample' => 41, 'min' => 20, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            4 => ['mean' => 124.22, 'sd' => 26.11, 'sample' => 64, 'min' => 30, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            5 => ['mean' => 122.43, 'sd' => 31.37, 'sample' => 111, 'min' => 30, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            6 => ['mean' => 122.73, 'sd' => 24.64, 'sample' => 143, 'min' => 30, 'max' => 150, 'note' => 'Nhóm mẫu khảo sát lớn nhất'],
            7 => ['mean' => 111.84, 'sd' => 33.54, 'sample' => 114, 'min' => 20, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            8 => ['mean' => 113.60, 'sd' => 24.77, 'sample' => 125, 'min' => 30, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            9 => ['mean' => 106.85, 'sd' => 33.04, 'sample' => 127, 'min' => 20, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            10 => ['mean' => 103.43, 'sd' => 29.18, 'sample' => 99, 'min' => 20, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            11 => ['mean' => 105.00, 'sd' => 31.19, 'sample' => 96, 'min' => 30, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            12 => ['mean' => 101.82, 'sd' => 32.53, 'sample' => 66, 'min' => 20, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            13 => ['mean' => 88.83, 'sd' => 27.81, 'sample' => 60, 'min' => 30, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            14 => ['mean' => 96.36, 'sd' => 29.52, 'sample' => 55, 'min' => 20, 'max' => 150, 'note' => 'Dữ liệu baseline khảo sát 2026'],
            15 => ['mean' => 93.09, 'sd' => 39.25, 'sample' => 55, 'min' => 20, 'max' => 150, 'note' => 'Đã gộp nhóm 15 và 16 tuổi (N=55)'],
            16 => ['mean' => 93.09, 'sd' => 39.25, 'sample' => 55, 'min' => 20, 'max' => 150, 'note' => 'Dùng chung chuẩn gộp 15-16 tuổi (N=55)'],
        ];

        foreach ($baselineData as $age => $item) {
            IqNorm::updateOrCreate(
                ['age' => $age],
                [
                    'mean_score' => $item['mean'],
                    'sd_score' => $item['sd'],
                    'sample_size' => $item['sample'],
                    'min_raw_score' => $item['min'],
                    'max_raw_score' => $item['max'],
                    'version' => 'baseline_v1',
                    'status' => ActiveStatus::Active,
                    'note' => $item['note'],
                ]
            );
        }
    }
}
