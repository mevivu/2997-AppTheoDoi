<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Bảng chuẩn baseline_v1 được lấy từ thang điểm cũ (20 - 150) nên không dùng được cho điểm thô 0 - 18 câu.
     * Chuyển sang chuẩn tạm thời theo % câu đúng (Mean 55%, SD 20%). Chỉ ghi đè bản ghi baseline_v1 chưa bị chỉnh tay.
     */
    public function up(): void
    {
        DB::table('iq_norms')
            ->where('version', 'baseline_v1')
            ->update([
                'mean_score' => 55.00,
                'sd_score' => 20.00,
                'sample_size' => 0,
                'min_raw_score' => 0,
                'max_raw_score' => 100,
                'version' => 'baseline_v2',
                'note' => 'Chuẩn tạm thời theo % câu đúng (Mean 55%, SD 20%)',
                'updated_at' => now(),
            ]);

        Cache::forget('iq_norms_all_active');
    }

    public function down(): void
    {
        // Không khôi phục: dữ liệu cũ sai đơn vị.
    }
};
