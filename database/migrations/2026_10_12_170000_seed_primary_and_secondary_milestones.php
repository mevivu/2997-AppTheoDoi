<?php

use App\Enums\Journal\MilestoneStage;
use App\Enums\Permission\PermissionType;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = Carbon::now();

        $milestones = [
            // Bổ sung cho Mẫu giáo (3–6 tuổi)
            [
                'stage' => MilestoneStage::Preschool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Biết đánh răng',
                'slug' => 'tu-danh-rang',
                'icon' => '🪥',
                'age_from_month' => 36,
                'age_to_month' => 72,
                'position' => 8,
            ],
            [
                'stage' => MilestoneStage::Preschool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Rụng chiếc răng sữa đầu tiên',
                'slug' => 'rung-chiec-rang-sua-dau-tien',
                'icon' => '🦷',
                'age_from_month' => 60,
                'age_to_month' => 72,
                'position' => 12,
            ],
            [
                'stage' => MilestoneStage::Preschool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Hoàn thành chương trình mẫu giáo',
                'slug' => 'hoan-thanh-chuong-trinh-mau-giao',
                'icon' => '🎓',
                'age_from_month' => 60,
                'age_to_month' => 72,
                'position' => 13,
            ],

            // Giai đoạn tiểu học (6–11 tuổi)
            [
                'stage' => MilestoneStage::PrimarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Vào lớp 1',
                'slug' => 'vao-lop-1',
                'icon' => '🎒',
                'age_from_month' => 72,
                'age_to_month' => 84,
                'position' => 1,
            ],
            [
                'stage' => MilestoneStage::PrimarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Đọc thành thạo',
                'slug' => 'doc-thanh-thao',
                'icon' => '📖',
                'age_from_month' => 72,
                'age_to_month' => 96,
                'position' => 2,
            ],
            [
                'stage' => MilestoneStage::PrimarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Viết thành thạo',
                'slug' => 'viet-thanh-thao',
                'icon' => '✏️',
                'age_from_month' => 72,
                'age_to_month' => 96,
                'position' => 3,
            ],
            [
                'stage' => MilestoneStage::PrimarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Có người bạn thân đầu tiên',
                'slug' => 'co-nguoi-ban-than-dau-tien',
                'icon' => '🤝',
                'age_from_month' => 72,
                'age_to_month' => 132,
                'position' => 4,
            ],
            [
                'stage' => MilestoneStage::PrimarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Đạt giải thưởng đầu tiên',
                'slug' => 'dat-giai-thuong-dau-tien',
                'icon' => '🏆',
                'age_from_month' => 72,
                'age_to_month' => 132,
                'position' => 5,
            ],
            [
                'stage' => MilestoneStage::PrimarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Biết đi xe đạp hai bánh',
                'slug' => 'biet-di-xe-dap-hai-banh',
                'icon' => '🚲',
                'age_from_month' => 72,
                'age_to_month' => 108,
                'position' => 6,
            ],
            [
                'stage' => MilestoneStage::PrimarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Hoàn thành chương trình Tiểu học',
                'slug' => 'hoan-thanh-chuong-trinh-tieu-hoc',
                'icon' => '🎖️',
                'age_from_month' => 120,
                'age_to_month' => 132,
                'position' => 7,
            ],

            // Giai đoạn trung học (11–18 tuổi)
            [
                'stage' => MilestoneStage::SecondarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Vào Trung học cơ sở',
                'slug' => 'vao-trung-hoc-co-so',
                'icon' => '🏫',
                'age_from_month' => 132,
                'age_to_month' => 144,
                'position' => 1,
            ],
            [
                'stage' => MilestoneStage::SecondarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Bước vào tuổi dậy thì',
                'slug' => 'buoc-vao-tuoi-day-thi',
                'icon' => '🌱',
                'age_from_month' => 132,
                'age_to_month' => 180,
                'position' => 2,
            ],
            [
                'stage' => MilestoneStage::SecondarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Đạt thành tích học tập nổi bật',
                'slug' => 'dat-thanh-tich-hoc-tap-noi-bat',
                'icon' => '⭐',
                'age_from_month' => 132,
                'age_to_month' => 216,
                'position' => 3,
            ],
            [
                'stage' => MilestoneStage::SecondarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Có chứng chỉ đầu tiên (ngoại ngữ, tin học...)',
                'slug' => 'co-chung-chi-dau-tien',
                'icon' => '📜',
                'age_from_month' => 132,
                'age_to_month' => 216,
                'position' => 4,
            ],
            [
                'stage' => MilestoneStage::SecondarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Vào Trung học phổ thông',
                'slug' => 'vao-trung-hoc-pho-thong',
                'icon' => '🏛️',
                'age_from_month' => 180,
                'age_to_month' => 192,
                'position' => 5,
            ],
            [
                'stage' => MilestoneStage::SecondarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Thi đỗ tốt nghiệp THPT',
                'slug' => 'thi-do-tot-nghiep-thpt',
                'icon' => '🎉',
                'age_from_month' => 204,
                'age_to_month' => 216,
                'position' => 6,
            ],
            [
                'stage' => MilestoneStage::SecondarySchool->value,
                'type' => PermissionType::ADMIN->value,
                'name' => 'Đỗ đại học hoặc chọn con đường nghề nghiệp',
                'slug' => 'do-dai-hoc-hoac-chon-con-duong-nghe-nghiep',
                'icon' => '🚀',
                'age_from_month' => 216,
                'age_to_month' => 228,
                'position' => 7,
            ],
        ];

        foreach ($milestones as $item) {
            DB::table('journal_milestones')->updateOrInsert(
                ['slug' => $item['slug']],
                array_merge($item, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('journal_milestones')
            ->whereIn('slug', [
                'rung-chiec-rang-sua-dau-tien',
                'hoan-thanh-chuong-trinh-mau-giao',
                'vao-lop-1',
                'doc-thanh-thao',
                'viet-thanh-thao',
                'co-nguoi-ban-than-dau-tien',
                'dat-giai-thuong-dau-tien',
                'biet-di-xe-dap-hai-banh',
                'hoan-thanh-chuong-trinh-tieu-hoc',
                'vao-trung-hoc-co-so',
                'buoc-vao-tuoi-day-thi',
                'dat-thanh-tich-hoc-tap-noi-bat',
                'co-chung-chi-dau-tien',
                'vao-trung-hoc-pho-thong',
                'thi-do-tot-nghiep-thpt',
                'do-dai-hoc-hoac-chon-con-duong-nghe-nghiep',
            ])
            ->delete();
    }
};
