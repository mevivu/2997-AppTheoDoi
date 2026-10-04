<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            // 1. Kích hoạt tính năng
            [
                'setting_key' => 'parent_rank_active',
                'setting_name' => 'Kích hoạt hệ thống Phân hạng Bố mẹ',
                'plain_value' => '1',
                'desc' => 'Bật (1) hoặc Tắt (0) tính năng tính điểm và hiển thị phân hạng thành viên Bố mẹ.',
                'type_input' => 9, // Checkbox
                'type_data' => null,
                'group' => 12, // ParentRank
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 2. Trọng số 4 tiêu chí (%) - Tổng phải bằng 100%
            [
                'setting_key' => 'parent_rank_weight_usage',
                'setting_name' => 'Trọng số: Thời gian sử dụng app (%)',
                'plain_value' => '30',
                'desc' => 'Tỷ trọng đóng góp của thời gian dùng app trong tổng điểm (mặc định 30%).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_weight_frequency',
                'setting_name' => 'Trọng số: Tần suất sử dụng app (%)',
                'plain_value' => '25',
                'desc' => 'Tỷ trọng đóng góp của số ngày vào app trong tổng điểm (mặc định 25%).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_weight_assessment',
                'setting_name' => 'Trọng số: Số bài đánh giá cho con (%)',
                'plain_value' => '25',
                'desc' => 'Tỷ trọng đóng góp của số bài đánh giá đã làm cho con (mặc định 25%).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_weight_child_score',
                'setting_name' => 'Trọng số: Điểm tổng hợp các chỉ số con (%)',
                'plain_value' => '20',
                'desc' => 'Tỷ trọng đóng góp của kết quả chỉ số phát triển con (mặc định 20%).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 3. Mục tiêu tiêu chuẩn tháng (Đạt mốc này = 100 điểm thành phần)
            [
                'setting_key' => 'parent_rank_target_usage_minutes',
                'setting_name' => 'Mục tiêu: Thời gian dùng app chuẩn/tháng (phút)',
                'plain_value' => '600',
                'desc' => 'Tổng số phút dùng app trong tháng để đạt điểm tối đa tiêu chí này (mặc định 600 phút = 10 giờ).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_target_active_days',
                'setting_name' => 'Mục tiêu: Số ngày vào app chuẩn/tháng (ngày)',
                'plain_value' => '20',
                'desc' => 'Số ngày mở app trong tháng để đạt điểm tối đa tiêu chí này (mặc định 20 ngày/tháng).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_target_assessments',
                'setting_name' => 'Mục tiêu: Số bài đánh giá chuẩn/tháng (bài)',
                'plain_value' => '5',
                'desc' => 'Số bài đánh giá thực hiện cho con trong tháng để đạt điểm tối đa tiêu chí này (mặc định 5 bài/tháng).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 4. Ngưỡng điểm để đạt từng cấp bậc (Tổng điểm 0 - 100)
            [
                'setting_key' => 'parent_rank_points_bronze',
                'setting_name' => 'Điểm sàn đạt Hạng Đồng',
                'plain_value' => '20',
                'desc' => 'Tổng điểm tối thiểu để được xếp hạng Đồng (mặc định 20 điểm).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_points_silver',
                'setting_name' => 'Điểm sàn đạt Hạng Bạc',
                'plain_value' => '40',
                'desc' => 'Tổng điểm tối thiểu để được xếp hạng Bạc (mặc định 40 điểm).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_points_gold',
                'setting_name' => 'Điểm sàn đạt Hạng Vàng',
                'plain_value' => '60',
                'desc' => 'Tổng điểm tối thiểu để được xếp hạng Vàng (mặc định 60 điểm).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_points_diamond',
                'setting_name' => 'Điểm sàn đạt Hạng Kim Cương',
                'plain_value' => '80',
                'desc' => 'Tổng điểm tối thiểu để được xếp hạng Kim Cương (mặc định 80 điểm).',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'parent_rank_active',
            'parent_rank_weight_usage',
            'parent_rank_weight_frequency',
            'parent_rank_weight_assessment',
            'parent_rank_weight_child_score',
            'parent_rank_target_usage_minutes',
            'parent_rank_target_active_days',
            'parent_rank_target_assessments',
            'parent_rank_points_bronze',
            'parent_rank_points_silver',
            'parent_rank_points_gold',
            'parent_rank_points_diamond',
        ];

        DB::table('settings')->whereIn('setting_key', $keys)->delete();
    }
};
