<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bổ sung trường số lần xem bài học/video vào parent_rank_snapshots nếu chưa có
        if (Schema::hasTable('parent_rank_snapshots') && !Schema::hasColumn('parent_rank_snapshots', 'lesson_video_views')) {
            Schema::table('parent_rank_snapshots', function (Blueprint $table) {
                $table->unsignedSmallInteger('lesson_video_views')->default(0)->after('assessment_count')
                    ->comment('Số lần xem bài học và video giáo dục trong kỳ');
            });
        }

        // 2. Thêm hoặc cập nhật các setting phân hạng 4 cấp (A/B/C/D)
        $newSettings = [
            [
                'setting_key' => 'parent_rank_points_c',
                'setting_name' => 'Điểm sàn đạt Hạng C (Bố mẹ đang cố gắng)',
                'plain_value' => '40',
                'desc' => 'Tổng điểm tối thiểu để được xếp Hạng C (mặc định 40 điểm).',
                'type_input' => 2,
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_points_b',
                'setting_name' => 'Điểm sàn đạt Hạng B (Bố mẹ tích cực)',
                'plain_value' => '60',
                'desc' => 'Tổng điểm tối thiểu để được xếp Hạng B (mặc định 60 điểm).',
                'type_input' => 2,
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_points_a',
                'setting_name' => 'Điểm sàn đạt Hạng A (Bố mẹ gương mẫu)',
                'plain_value' => '80',
                'desc' => 'Tổng điểm tối thiểu để được xếp Hạng A (mặc định 80 điểm).',
                'type_input' => 2,
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_target_lesson_video_views',
                'setting_name' => 'Mục tiêu: Số lần xem bài học/video chuẩn/tháng',
                'plain_value' => '20',
                'desc' => 'Số lần xem bài học/video trong tháng để đạt tiêu chuẩn không bị cảnh báo (mặc định 20 lần/tháng).',
                'type_input' => 2,
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($newSettings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }

        // 3. Chuyển đổi dữ liệu cấp bậc cũ (0: New, 1: Bronze, 2: Silver, 3: Gold, 4: Diamond)
        // sang 4 cấp bậc mới (0: D, 1: C, 2: B, 3: A)
        if (Schema::hasTable('parent_rank_snapshots')) {
            DB::table('parent_rank_snapshots')->where('rank', 4)->update(['rank' => 3]);
            DB::table('parent_rank_snapshots')->where('rank', 3)->update(['rank' => 2]);
            DB::table('parent_rank_snapshots')->where('rank', 2)->update(['rank' => 1]);
            DB::table('parent_rank_snapshots')->where('rank', 1)->update(['rank' => 0]);
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'parent_rank')) {
            DB::table('users')->where('parent_rank', 4)->update(['parent_rank' => 3]);
            DB::table('users')->where('parent_rank', 3)->update(['parent_rank' => 2]);
            DB::table('users')->where('parent_rank', 2)->update(['parent_rank' => 1]);
            DB::table('users')->where('parent_rank', 1)->update(['parent_rank' => 0]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parent_rank_snapshots') && Schema::hasColumn('parent_rank_snapshots', 'lesson_video_views')) {
            Schema::table('parent_rank_snapshots', function (Blueprint $table) {
                $table->dropColumn('lesson_video_views');
            });
        }

        DB::table('settings')->whereIn('setting_key', [
            'parent_rank_points_c',
            'parent_rank_points_b',
            'parent_rank_points_a',
            'parent_rank_target_lesson_video_views',
        ])->delete();
    }
};
