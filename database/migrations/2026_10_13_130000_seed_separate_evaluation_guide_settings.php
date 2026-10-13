<?php

use App\Enums\Setting\SettingGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function settings(): array
    {
        $now = now();
        $group = defined(SettingGroup::class . '::Evaluation') ? SettingGroup::Evaluation : 13;

        // Lấy video hiện có nếu đã upload để làm giá trị khởi tạo
        $existingVideo = DB::table('settings')->where('setting_key', 'evaluation_guide_video')->value('plain_value');
        $defaultVideo = $existingVideo ?: 'https://pub-0a0907e25bdf46c788b33a1821471f4f.r2.dev/videos/evaluations/NXF5U8nqMSaFgU3lWp75tK25G04bL2O3QhZkVh5n.mp4';

        return [
            // --- Cấu hình Đánh giá EQ ---
            [
                'setting_key' => 'evaluation_guide_video_eq',
                'setting_name' => 'Video hướng dẫn đánh giá EQ',
                'plain_value' => $defaultVideo,
                'desc' => 'Đường dẫn video hướng dẫn phụ huynh trước khi thực hiện bài đánh giá EQ (Cloudflare R2 hoặc YouTube)',
                'type_input' => 1,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'evaluation_guide_title_eq',
                'setting_name' => 'Tiêu đề câu hỏi định nghĩa EQ',
                'plain_value' => 'Chỉ số EQ là gì?',
                'desc' => 'Tiêu đề câu hỏi hiển thị trong thẻ định nghĩa đánh giá EQ trên App',
                'type_input' => 1,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'evaluation_guide_desc_eq',
                'setting_name' => 'Nội dung giải thích định nghĩa EQ',
                'plain_value' => 'EQ (Emotional Quotient) là chỉ số cảm xúc, thể hiện khả năng nhận biết, thấu hiểu, quản lý cảm xúc của bản thân và kết nối, đồng cảm với những người xung quanh. Một đứa trẻ có EQ tốt sẽ tự tin, dễ hòa nhập và biết cách sẻ chia.',
                'desc' => 'Nội dung chi tiết giải thích cho phụ huynh hiểu rõ chỉ số EQ là gì',
                'type_input' => 6,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'evaluation_guide_note_eq',
                'setting_name' => 'Lời khuyên & Lưu ý đánh giá EQ',
                'plain_value' => 'Ba mẹ vui lòng quan sát hành vi và phản ứng cảm xúc thực tế của bé trong sinh hoạt thường ngày gần nhất để chọn đáp án khách quan nhất.',
                'desc' => 'Lời khuyên hiển thị cho phụ huynh trước khi bắt đầu bài đánh giá EQ',
                'type_input' => 6,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // --- Cấu hình Đánh giá AQ ---
            [
                'setting_key' => 'evaluation_guide_video_aq',
                'setting_name' => 'Video hướng dẫn đánh giá AQ',
                'plain_value' => $defaultVideo,
                'desc' => 'Đường dẫn video hướng dẫn phụ huynh trước khi thực hiện bài đánh giá AQ (Cloudflare R2 hoặc YouTube)',
                'type_input' => 1,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'evaluation_guide_title_aq',
                'setting_name' => 'Tiêu đề câu hỏi định nghĩa AQ',
                'plain_value' => 'Chỉ số AQ là gì?',
                'desc' => 'Tiêu đề câu hỏi hiển thị trong thẻ định nghĩa đánh giá AQ trên App',
                'type_input' => 1,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'evaluation_guide_desc_aq',
                'setting_name' => 'Nội dung giải thích định nghĩa AQ',
                'plain_value' => 'AQ (Adversity Quotient) là chỉ số vượt khó, đo lường khả năng kiên trì, bản lĩnh đối mặt và cách con vượt qua khó khăn, trở ngại trong học tập và cuộc sống. Trẻ có AQ cao sẽ kiên trì, không dễ bỏ cuộc khi gặp thất bại.',
                'desc' => 'Nội dung chi tiết giải thích cho phụ huynh hiểu rõ chỉ số AQ là gì',
                'type_input' => 6,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'evaluation_guide_note_aq',
                'setting_name' => 'Lời khuyên & Lưu ý đánh giá AQ',
                'plain_value' => 'Ba mẹ hãy nhớ lại cách bé phản ứng khi gặp một bài toán khó, một món đồ chơi bị hỏng hoặc một mục tiêu chưa đạt được để chọn đáp án đúng với tính cách của con.',
                'desc' => 'Lời khuyên hiển thị cho phụ huynh trước khi bắt đầu bài đánh giá AQ',
                'type_input' => 6,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // --- Cấu hình Kiểm tra IQ ---
            [
                'setting_key' => 'evaluation_guide_video_iq',
                'setting_name' => 'Video hướng dẫn kiểm tra IQ',
                'plain_value' => $defaultVideo,
                'desc' => 'Đường dẫn video hướng dẫn trước khi thực hiện bài kiểm tra IQ (Cloudflare R2 hoặc YouTube)',
                'type_input' => 1,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'evaluation_guide_title_iq',
                'setting_name' => 'Tiêu đề câu hỏi định nghĩa IQ',
                'plain_value' => 'Chỉ số IQ là gì?',
                'desc' => 'Tiêu đề câu hỏi hiển thị trong thẻ định nghĩa kiểm tra IQ trên App',
                'type_input' => 1,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'evaluation_guide_desc_iq',
                'setting_name' => 'Nội dung giải thích định nghĩa IQ',
                'plain_value' => 'IQ (Intelligence Quotient) là chỉ số thông minh, đánh giá tư duy logic, phản xạ trí tuệ, khả năng nhận thức quy luật hình khối và năng lực giải quyết vấn đề của trẻ.',
                'desc' => 'Nội dung chi tiết giải thích chỉ số IQ là gì',
                'type_input' => 6,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'evaluation_guide_note_iq',
                'setting_name' => 'Lời khuyên & Lưu ý kiểm tra IQ',
                'plain_value' => 'Ba mẹ hãy tạo cho bé không gian yên tĩnh, tâm lý thoải mái và không gợi ý đáp án để kết quả phản ánh chính xác tư duy của con.',
                'desc' => 'Lời khuyên hiển thị cho phụ huynh trước khi bắt đầu bài kiểm tra IQ',
                'type_input' => 6,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
    }

    public function up(): void
    {
        $group = defined(SettingGroup::class . '::Evaluation') ? SettingGroup::Evaluation : 13;

        // Đảm bảo các cài đặt evaluation cũ cũng thuộc group Evaluation
        DB::table('settings')
            ->whereIn('setting_key', ['evaluation_guide_video', 'evaluation_guide_note'])
            ->update(['group' => $group]);

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
