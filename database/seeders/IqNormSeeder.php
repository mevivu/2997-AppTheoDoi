<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Models\IqNorm;
use Illuminate\Database\Seeder;

class IqNormSeeder extends Seeder
{
    /**
     * Bảng chuẩn tạm thời (baseline_v2) tính theo % câu đúng (0 - 100):
     * - Mean = 55% , SD = 20%  => làm đúng 100% ≈ IQ 134, đúng 55% = IQ 100, sai hết ≈ IQ 59.
     * Khi có đủ dữ liệu thực tế, dùng nút "Tái chuẩn hóa" trong CMS để cập nhật theo từng độ tuổi.
     */
    public function run(): void
    {
        for ($age = 1; $age <= 20; $age++) {
            IqNorm::updateOrCreate(
                ['age' => $age],
                [
                    'mean_score' => 55.00,
                    'sd_score' => 20.00,
                    'sample_size' => 0,
                    'min_raw_score' => 0,
                    'max_raw_score' => 100,
                    'version' => 'baseline_v2',
                    'status' => ActiveStatus::Active,
                    'note' => 'Chuẩn tạm thời theo % câu đúng (Mean 55%, SD 20%)',
                ]
            );
        }
    }
}
