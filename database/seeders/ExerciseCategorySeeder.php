<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseTopic;
use App\Models\AgeGroup;
use App\Models\ExerciseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExerciseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ageGroups = AgeGroup::all()->keyBy('id');

        $categories = [
            // Thai giáo
            [
                'topic' => ExerciseTopic::THAI_GIAO->value,
                'age_group_id' => 1,
                'name' => 'Thai giáo vận động nhẹ nhàng',
                'sort_order' => 1,
            ],
            [
                'topic' => ExerciseTopic::THAI_GIAO->value,
                'age_group_id' => 1,
                'name' => 'Thai giáo cảm xúc & Âm nhạc',
                'sort_order' => 2,
            ],
            [
                'topic' => ExerciseTopic::THAI_GIAO->value,
                'age_group_id' => 1,
                'name' => 'Thai giáo liên kết & Tương tác xúc giác',
                'sort_order' => 3,
            ],

            // PQ - Thể chất
            [
                'topic' => ExerciseTopic::PQ->value,
                'age_group_id' => 2, // 0-2 tuổi
                'name' => 'Vận động thô đầu đời (Lẫy, Bò, Đi)',
                'sort_order' => 1,
            ],
            [
                'topic' => ExerciseTopic::PQ->value,
                'age_group_id' => 2,
                'name' => 'Vận động tinh & Phối hợp tay mắt',
                'sort_order' => 2,
            ],
            [
                'topic' => ExerciseTopic::PQ->value,
                'age_group_id' => 3, // 2-4 tuổi
                'name' => 'Thăng bằng & Phối hợp vận động toàn thân',
                'sort_order' => 3,
            ],
            [
                'topic' => ExerciseTopic::PQ->value,
                'age_group_id' => 4, // 4-6 tuổi
                'name' => 'Rèn luyện phản xạ & Nhanh nhẹn',
                'sort_order' => 4,
            ],
            [
                'topic' => ExerciseTopic::PQ->value,
                'age_group_id' => 5, // 6-8 tuổi
                'name' => 'Sức bền & Tăng trưởng chiều cao',
                'sort_order' => 5,
            ],

            // IQ - Trí tuệ
            [
                'topic' => ExerciseTopic::IQ->value,
                'age_group_id' => 2, // 0-2 tuổi
                'name' => 'Kích thích giác quan & Nhận thức sự vật',
                'sort_order' => 1,
            ],
            [
                'topic' => ExerciseTopic::IQ->value,
                'age_group_id' => 3, // 2-4 tuổi
                'name' => 'Nhận biết quy luật, Hình khối & Màu sắc',
                'sort_order' => 2,
            ],
            [
                'topic' => ExerciseTopic::IQ->value,
                'age_group_id' => 4, // 4-6 tuổi
                'name' => 'Tư duy logic & Phân loại đối tượng',
                'sort_order' => 3,
            ],
            [
                'topic' => ExerciseTopic::IQ->value,
                'age_group_id' => 5, // 6-8 tuổi
                'name' => 'Ghi nhớ không gian & Giải đố mê cung',
                'sort_order' => 4,
            ],
            [
                'topic' => ExerciseTopic::IQ->value,
                'age_group_id' => 6, // 8-10 tuổi
                'name' => 'Tư duy toán học & Giải quyết vấn đề',
                'sort_order' => 5,
            ],

            // EQ - Cảm xúc
            [
                'topic' => ExerciseTopic::EQ->value,
                'age_group_id' => 2, // 0-2 tuổi
                'name' => 'Gắn kết an toàn & Biểu đạt cảm xúc',
                'sort_order' => 1,
            ],
            [
                'topic' => ExerciseTopic::EQ->value,
                'age_group_id' => 3, // 2-4 tuổi
                'name' => 'Nhận diện cảm xúc (Vui, Buồn, Giận, Sợ)',
                'sort_order' => 2,
            ],
            [
                'topic' => ExerciseTopic::EQ->value,
                'age_group_id' => 4, // 4-6 tuổi
                'name' => 'Sẻ chia, Nhường nhịn & Kỹ năng kết bạn',
                'sort_order' => 3,
            ],
            [
                'topic' => ExerciseTopic::EQ->value,
                'age_group_id' => 5, // 6-8 tuổi
                'name' => 'Thấu cảm & Lắng nghe tích cực',
                'sort_order' => 4,
            ],
            [
                'topic' => ExerciseTopic::EQ->value,
                'age_group_id' => 6, // 8-10 tuổi
                'name' => 'Kiểm soát xung đột & Giao tiếp hòa giải',
                'sort_order' => 5,
            ],

            // AQ - Vượt khó
            [
                'topic' => ExerciseTopic::AQ->value,
                'age_group_id' => 3, // 2-4 tuổi
                'name' => 'Tập tự đứng dậy khi vấp ngã',
                'sort_order' => 1,
            ],
            [
                'topic' => ExerciseTopic::AQ->value,
                'age_group_id' => 4, // 4-6 tuổi
                'name' => 'Kiên nhẫn hoàn thành nhiệm vụ đến cùng',
                'sort_order' => 2,
            ],
            [
                'topic' => ExerciseTopic::AQ->value,
                'age_group_id' => 5, // 6-8 tuổi
                'name' => 'Đối diện với thất bại & Thử lại khi sai',
                'sort_order' => 3,
            ],
            [
                'topic' => ExerciseTopic::AQ->value,
                'age_group_id' => 6, // 8-10 tuổi
                'name' => 'Vượt qua nỗi sợ & Đón nhận thử thách mới',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $cat) {
            if (!$ageGroups->has($cat['age_group_id'])) {
                continue;
            }

            ExerciseCategory::updateOrCreate(
                [
                    'topic' => $cat['topic'],
                    'name' => $cat['name'],
                ],
                [
                    'age_group_id' => $cat['age_group_id'],
                    'parent_id' => null,
                    'slug' => Str::slug($cat['name']),
                    'icon' => null,
                    'sort_order' => $cat['sort_order'],
                    'status' => ActiveStatus::Active,
                ]
            );
        }
    }
}
