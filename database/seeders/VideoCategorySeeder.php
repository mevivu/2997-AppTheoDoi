<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Models\AgeGroup;
use App\Models\VideoCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VideoCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ageGroups = AgeGroup::all()->keyBy('id');

        $categoriesData = [
            // 1: Thai giáo
            1 => [
                'Thai giáo âm nhạc & cảm xúc',
                'Nhạc cổ điển kích thích não bộ',
                'Lời thủ thỉ và kể chuyện cho thai nhi',
                'Dinh dưỡng & Sức khỏe mẹ bầu',
                'Yoga & Vận động nhẹ nhàng cho mẹ',
                'Kết nối yêu thương Bố Mẹ và Bé',
            ],

            // 2: 0-2 tuổi
            2 => [
                'Vận động thô & Vận động tinh sớm',
                'Tummy time & Tập lẫy, tập bò',
                'Vận động ngón tay & Cầm nắm',
                'Giác quan & Nhận biết đầu đời',
                'Dinh dưỡng ăn dặm & Giấc ngủ sinh học',
                'Âm nhạc & Phát triển ngôn ngữ sớm',
            ],

            // 3: 2-4 tuổi
            3 => [
                'Khám phá thế giới quanh em',
                'Thế giới động vật ngộ nghĩnh',
                'Nhận biết màu sắc & Hình khối',
                'Kỹ năng tự lập đầu đời',
                'Bài hát thiếu nhi & Vận động theo nhạc',
                'Kể chuyện & Mở rộng vốn từ',
            ],

            // 4: 4-6 tuổi
            4 => [
                'Hành trang vào lớp 1',
                'Làm quen bảng chữ cái tiếng Việt',
                'Làm quen chữ số & Đếm số lượng',
                'Phát triển tư duy logic (IQ)',
                'Quản lý cảm xúc & Giao tiếp (EQ)',
                'Sáng tạo mỹ thuật & Thủ công',
            ],

            // 5: 6-8 tuổi
            5 => [
                'Kỹ năng sống & Tự bảo vệ bản thân',
                'Khoa học vui & Khám phá tự nhiên',
                'Thí nghiệm khoa học vui tại nhà',
                'Bí ẩn vũ trụ & Trái Đất',
                'Phương pháp rèn luyện tập trung & Ghi nhớ',
                'Văn hóa đọc & Đọc sách sáng tạo',
            ],

            // 6: 8-10 tuổi
            6 => [
                'Khoa học - Công nghệ & STEM',
                'Kỹ năng làm việc nhóm & Thuyết trình',
                'Tư duy phản biện & Vượt khó (AQ)',
                'Quản lý tài chính cá nhân cho trẻ',
            ],

            // 7: 10-12 tuổi
            7 => [
                'Tâm sinh lý tiền dậy thì & Chăm sóc bản thân',
                'Kỹ năng tự học & Quản lý thời gian',
                'Rèn luyện thể chất & Thói quen lành mạnh',
            ],

            // 8: 12-14 tuổi
            8 => [
                'Thấu hiểu bản thân & Giải tỏa áp lực học đường',
                'Kỹ năng ứng xử & Tình bạn học đường',
                'An toàn & Ứng xử trên không gian mạng',
            ],

            // 9: 14+ tuổi
            9 => [
                'Định hướng năng khiếu & Nghề nghiệp tương lai',
                'Kỹ năng mềm & Lãnh đạo bản thân',
                'Tư duy độc lập & Chuẩn bị tự lập',
            ],
        ];

        foreach ($categoriesData as $ageGroupId => $categoryNames) {
            if (!$ageGroups->has($ageGroupId)) {
                continue;
            }

            foreach ($categoryNames as $index => $name) {
                VideoCategory::updateOrCreate(
                    [
                        'age_group_id' => $ageGroupId,
                        'name' => $name,
                    ],
                    [
                        'parent_id' => null,
                        'slug' => Str::slug($name),
                        'icon' => null,
                        'sort_order' => $index + 1,
                        'status' => ActiveStatus::Active,
                    ]
                );
            }
        }
    }
}
