<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Danh sách setting cấu hình cảnh báo & gợi ý "Gợi ý từ Chăm Con 360"
     */
    private function settings(): array
    {
        $now = now();

        $row = fn(string $key, string $name, string $value, string $desc, int $type) => [
            'setting_key' => $key,
            'setting_name' => $name,
            'plain_value' => $value,
            'desc' => $desc,
            'type_input' => $type, // 2 = Number, 6 = Textarea, 9 = Checkbox
            'type_data' => null,
            'group' => 12, // ParentRank
            'created_at' => $now,
            'updated_at' => $now,
        ];

        return [
            // Ngưỡng
            $row('parent_rank_warning_ratio', 'Cảnh báo: Ngưỡng % so với mục tiêu tháng', '50',
                'Nếu số liệu trong tháng thấp hơn mức % này so với mục tiêu thì hiển thị gợi ý (mặc định 50%).', 2),
            $row('parent_rank_competency_threshold', 'Cảnh báo: Ngưỡng điểm chỉ số năng lực yếu', '5',
                'Chỉ số IQ/EQ/AQ/PQ/GPA (thang 0-10) thấp hơn mức này sẽ được nhắc cải thiện (mặc định 5).', 2),
            $row('parent_rank_competency_max_display', 'Cảnh báo: Số lĩnh vực yếu tối đa hiển thị', '2',
                'Số lĩnh vực có điểm thấp nhất tối đa được nêu trong câu gợi ý (mặc định 2).', 2),

            // Bật / tắt
            $row('parent_rank_suggest_enable_usage', 'Gợi ý: Bật cảnh báo thời gian dùng app / số ngày vào app', '1',
                'Bật (1) hoặc tắt (0) gợi ý khi thời gian dùng app hoặc số ngày vào app thấp.', 9),
            $row('parent_rank_suggest_enable_assessment', 'Gợi ý: Bật cảnh báo số bài đánh giá', '1',
                'Bật (1) hoặc tắt (0) gợi ý khi số bài đánh giá cho con thấp.', 9),
            $row('parent_rank_suggest_enable_content', 'Gợi ý: Bật cảnh báo lượt xem bài học & video', '1',
                'Bật (1) hoặc tắt (0) gợi ý khi lượt xem bài học/video thấp.', 9),
            $row('parent_rank_suggest_enable_competency', 'Gợi ý: Bật cảnh báo chỉ số năng lực yếu', '1',
                'Bật (1) hoặc tắt (0) gợi ý khi con có chỉ số năng lực dưới ngưỡng.', 9),

            // Nội dung câu
            $row('parent_rank_suggest_text_usage', 'Gợi ý: Nội dung - thời gian dùng app / số ngày vào app',
                'Bố mẹ nên dành thêm thời gian kiểm tra các chỉ số và năng lực của con thường xuyên để theo dõi sự phát triển.',
                'Câu hiển thị khi thời gian dùng app hoặc số ngày vào app thấp hơn ngưỡng.', 6),
            $row('parent_rank_suggest_text_assessment', 'Gợi ý: Nội dung - số bài đánh giá',
                'Bố mẹ nên cập nhật chiều cao và cân nặng cho con hàng tháng và các chỉ số IQ, AQ, EQ, học bạ điện tử định kỳ để theo dõi tăng trưởng chính xác hơn.',
                'Câu hiển thị khi số bài đánh giá cho con thấp hơn ngưỡng.', 6),
            $row('parent_rank_suggest_text_content', 'Gợi ý: Nội dung - lượt xem bài học & video',
                'Bố mẹ nên cho con luyện tập thêm các bài học và bài tập trong ứng dụng để phát triển các năng lực.',
                'Câu hiển thị khi lượt xem bài học/video thấp hơn ngưỡng.', 6),
            $row('parent_rank_suggest_text_competency', 'Gợi ý: Nội dung - chỉ số năng lực yếu',
                '{child} cần tiếp tục luyện tập để cải thiện {label}: {list}.',
                'Câu hiển thị khi con có chỉ số dưới ngưỡng. Biến: {child} (tên bé), {label} (chỉ số / các chỉ số), {list} (VD: EQ = 2, GPA = 3).', 6),
        ];
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->settings() as $setting) {
            // Không ghi đè nếu admin đã có giá trị
            $exists = DB::table('settings')->where('setting_key', $setting['setting_key'])->exists();
            if (!$exists) {
                DB::table('settings')->insert($setting);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->whereIn('setting_key', array_column($this->settings(), 'setting_key'))
            ->delete();
    }
};
