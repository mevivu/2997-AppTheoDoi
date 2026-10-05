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
        // 1. Bổ sung trường điểm thành phần lượt xem bài học/video vào parent_rank_snapshots
        if (Schema::hasTable('parent_rank_snapshots') && !Schema::hasColumn('parent_rank_snapshots', 'score_lesson_video')) {
            Schema::table('parent_rank_snapshots', function (Blueprint $table) {
                $table->decimal('score_lesson_video', 5, 2)->default(0.00)->after('score_assessment')
                    ->comment('Điểm thành phần xem bài học và video giáo dục (thang điểm 100)');
            });
        }

        // 2. Thêm setting trọng số cho tiêu chí xem bài học/video (mặc định 15%)
        $newSettings = [
            [
                'setting_key' => 'parent_rank_weight_lesson_video',
                'setting_name' => 'Trọng số: Lượt xem bài học & video giáo dục (%)',
                'plain_value' => '15',
                'desc' => 'Đóng góp của số lượt xem bài học và video trong tháng (mặc định: 15%).',
                'type_input' => 2,
                'type_data' => null,
                'group' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'parent_rank_target_lesson_video_views',
                'setting_name' => 'Mục tiêu: Số lượt xem bài học/video chuẩn/tháng',
                'plain_value' => '20',
                'desc' => 'Số lượt xem bài học/video trong tháng để đạt 100 điểm thành phần (mặc định 20 lượt/tháng).',
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

        // 3. Cân bằng lại 4 trọng số còn lại để tổng 5 trọng số = 100%
        // (Usage: 25%, Frequency: 20%, Assessment: 20%, LessonVideo: 15%, ChildScore: 20% => Tổng = 100%)
        DB::table('settings')->where('setting_key', 'parent_rank_weight_usage')->update(['plain_value' => '25']);
        DB::table('settings')->where('setting_key', 'parent_rank_weight_frequency')->update(['plain_value' => '20']);
        DB::table('settings')->where('setting_key', 'parent_rank_weight_assessment')->update(['plain_value' => '20']);
        DB::table('settings')->where('setting_key', 'parent_rank_weight_child_score')->update(['plain_value' => '20']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parent_rank_snapshots') && Schema::hasColumn('parent_rank_snapshots', 'score_lesson_video')) {
            Schema::table('parent_rank_snapshots', function (Blueprint $table) {
                $table->dropColumn('score_lesson_video');
            });
        }

        DB::table('settings')->where('setting_key', 'parent_rank_weight_lesson_video')->delete();

        // Khôi phục 4 trọng số cũ (30%, 25%, 25%, 20%)
        DB::table('settings')->where('setting_key', 'parent_rank_weight_usage')->update(['plain_value' => '30']);
        DB::table('settings')->where('setting_key', 'parent_rank_weight_frequency')->update(['plain_value' => '25']);
        DB::table('settings')->where('setting_key', 'parent_rank_weight_assessment')->update(['plain_value' => '25']);
        DB::table('settings')->where('setting_key', 'parent_rank_weight_child_score')->update(['plain_value' => '20']);
    }
};
