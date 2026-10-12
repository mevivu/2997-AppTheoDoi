<?php

use App\Enums\Journal\MilestoneStage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        $milestones = [
            // Giai đoạn sơ sinh (0–1 tuổi): 11 cột mốc
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Sinh ra', 'slug' => 'sinh-ra', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 1],
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Mỉm cười đầu tiên', 'slug' => 'mim-cuoi-dau-tien', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 2],
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Biết lẫy', 'slug' => 'biet-lay', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 3],
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Biết ngồi', 'slug' => 'biet-ngoi', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 4],
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Biết trườn', 'slug' => 'biet-truon', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 5],
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Biết bò', 'slug' => 'biet-bo', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 6],
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Mọc chiếc răng đầu tiên', 'slug' => 'moc-chiec-rang-dau-tien', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 7],
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Biết vịn đứng', 'slug' => 'biet-vin-dung', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 8],
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Biết đứng', 'slug' => 'biet-dung', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 9],
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Biết đi', 'slug' => 'biet-di', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 10],
            ['stage' => MilestoneStage::Newborn->value, 'name' => 'Nói tiếng đầu tiên', 'slug' => 'noi-tieng-dau-tien', 'age_from_month' => 0, 'age_to_month' => 12, 'position' => 11],

            // Giai đoạn tập đi (1–3 tuổi): 11 cột mốc
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Biết chạy', 'slug' => 'biet-chay', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 1],
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Biết leo cầu thang', 'slug' => 'biet-leo-cau-thang', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 2],
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Tự xúc ăn', 'slug' => 'tu-xuc-an', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 3],
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Cai sữa', 'slug' => 'cai-sua', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 4],
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Bỏ bỉm', 'slug' => 'bo-bim', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 5],
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Biết gọi tên bố mẹ', 'slug' => 'biet-goi-ten-bo-me', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 6],
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Biết hát', 'slug' => 'biet-hat', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 7],
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Biết nhảy', 'slug' => 'biet-nhay', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 8],
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Biết vẽ', 'slug' => 'biet-ve', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 9],
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Đi học mẫu giáo', 'slug' => 'di-hoc-mau-giao', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 10],
            ['stage' => MilestoneStage::Toddler->value, 'name' => 'Con đã nói rõ', 'slug' => 'con-da-noi-ro', 'age_from_month' => 12, 'age_to_month' => 36, 'position' => 11],

            // Giai đoạn mẫu giáo (3–6 tuổi): 11 cột mốc
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Thuộc bảng chữ cái', 'slug' => 'thuoc-bang-chu-cai', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 1],
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Biết đếm số', 'slug' => 'biet-dem-so', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 2],
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Biết viết tên mình', 'slug' => 'biet-viet-ten-minh', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 3],
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Biết đọc những từ đầu tiên', 'slug' => 'biet-doc-nhung-tu-dau-tien', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 4],
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Biết đi xe đạp', 'slug' => 'biet-di-xe-dap', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 5],
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Biết bơi', 'slug' => 'biet-boi', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 6],
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Biết tự mặc quần áo', 'slug' => 'biet-tu-mac-quan-ao', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 7],
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Tự đánh răng', 'slug' => 'tu-danh-rang', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 8],
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Biết tự dọn đồ chơi', 'slug' => 'biet-tu-don-do-choi', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 9],
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Biết kể chuyện', 'slug' => 'biet-ke-chuyen', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 10],
            ['stage' => MilestoneStage::Preschool->value, 'name' => 'Tốt nghiệp mầm non', 'slug' => 'tot-nghiep-mam-non', 'age_from_month' => 36, 'age_to_month' => 72, 'position' => 11],
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

        // Permissions admin
        $module = [
            'name' => 'Quản lý Cột mốc nhật ký',
            'description' => 'Quản lý danh mục cột mốc nhật ký gợi ý theo giai đoạn phát triển của trẻ',
            'status' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('modules')->insertOrIgnore($module);
        $moduleId = DB::table('modules')->where('name', 'Quản lý Cột mốc nhật ký')->value('id');

        $permissions = [
            ['title' => 'Xem cột mốc nhật ký', 'name' => 'viewJournalMilestone', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $moduleId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo cột mốc nhật ký', 'name' => 'createJournalMilestone', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $moduleId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa cột mốc nhật ký', 'name' => 'updateJournalMilestone', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $moduleId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xoá cột mốc nhật ký', 'name' => 'deleteJournalMilestone', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $moduleId, 'created_at' => $now, 'updated_at' => $now],
        ];

        $permNames = array_column($permissions, 'name');
        DB::table('permissions')->insertOrIgnore($permissions);

        $adminRoles = DB::table('roles')->whereIn('name', ['superAdmin', 'admin'])->get();
        $insertedPerms = DB::table('permissions')->whereIn('name', $permNames)->get();

        $rolePerms = [];
        foreach ($adminRoles as $role) {
            foreach ($insertedPerms as $perm) {
                $rolePerms[] = [
                    'permission_id' => $perm->id,
                    'role_id' => $role->id,
                ];
            }
        }

        if (!empty($rolePerms)) {
            DB::table('role_has_permissions')->insertOrIgnore($rolePerms);
        }

        if (class_exists(PermissionRegistrar::class)) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public function down(): void
    {
        $permNames = ['viewJournalMilestone', 'createJournalMilestone', 'updateJournalMilestone', 'deleteJournalMilestone'];
        $permIds = DB::table('permissions')->whereIn('name', $permNames)->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('name', $permNames)->delete();
        DB::table('modules')->where('name', 'Quản lý Cột mốc nhật ký')->delete();

        if (class_exists(PermissionRegistrar::class)) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
};
