<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function settings(): array
    {
        $now = now();

        return [
            [
                'setting_key' => 'evaluation_guide_video',
                'setting_name' => 'Video hướng dẫn đánh giá',
                'plain_value' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                'desc' => 'Đường dẫn video hướng dẫn phụ huynh trước khi thực hiện các bài đánh giá cho trẻ',
                'type_input' => 1, // Text / URL
                'type_data' => null,
                'group' => 1, // SettingGroup::General
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'evaluation_guide_note',
                'setting_name' => 'Lưu ý trước khi đánh giá',
                'plain_value' => 'Ba mẹ vui lòng quan sát hành vi thực tế của bé trong 1 tháng gần nhất để chọn đáp án phản ánh chính xác nhất.',
                'desc' => 'Lời khuyên hoặc lưu ý hiển thị dưới video hướng dẫn trên ứng dụng di động',
                'type_input' => 6, // Textarea
                'type_data' => null,
                'group' => 1, // SettingGroup::General
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
    }

    public function up(): void
    {
        foreach ($this->settings() as $setting) {
            DB::table('settings')->updateOrInsert(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')
            ->whereIn('setting_key', array_column($this->settings(), 'setting_key'))
            ->delete();
    }
};
