<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        $modules = [
            ['name' => 'Quản lý Giới thiệu nền tảng', 'description' => 'Quản lý các bài giới thiệu, tầm nhìn, sứ mệnh, giá trị cốt lõi', 'status' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Quản lý Hồ sơ Chuyên gia', 'description' => 'Quản lý thông tin bác sĩ, chuyên gia cố vấn', 'status' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Quản lý Chuyên mục Chuyên gia', 'description' => 'Quản lý danh mục chuyên đề y khoa, giáo dục', 'status' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Quản lý Bài viết Chuyên gia', 'description' => 'Quản lý bài viết kiến thức và lời khuyên chuyên môn', 'status' => 2, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($modules as $mod) {
            DB::table('modules')->insertOrIgnore($mod);
        }

        $introModId = DB::table('modules')->where('name', 'Quản lý Giới thiệu nền tảng')->value('id');
        $expertModId = DB::table('modules')->where('name', 'Quản lý Hồ sơ Chuyên gia')->value('id');
        $catModId = DB::table('modules')->where('name', 'Quản lý Chuyên mục Chuyên gia')->value('id');
        $postModId = DB::table('modules')->where('name', 'Quản lý Bài viết Chuyên gia')->value('id');

        $permissions = [
            // Giới thiệu
            ['title' => 'Xem bài giới thiệu', 'name' => 'viewIntroduction', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $introModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo bài giới thiệu', 'name' => 'createIntroduction', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $introModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa bài giới thiệu', 'name' => 'updateIntroduction', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $introModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa bài giới thiệu', 'name' => 'deleteIntroduction', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $introModId, 'created_at' => $now, 'updated_at' => $now],

            // Hồ sơ Chuyên gia
            ['title' => 'Xem hồ sơ chuyên gia', 'name' => 'viewExpert', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $expertModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo hồ sơ chuyên gia', 'name' => 'createExpert', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $expertModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa hồ sơ chuyên gia', 'name' => 'updateExpert', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $expertModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa hồ sơ chuyên gia', 'name' => 'deleteExpert', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $expertModId, 'created_at' => $now, 'updated_at' => $now],

            // Danh mục chuyên đề
            ['title' => 'Xem danh mục chuyên gia', 'name' => 'viewExpertCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $catModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo danh mục chuyên gia', 'name' => 'createExpertCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $catModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa danh mục chuyên gia', 'name' => 'updateExpertCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $catModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa danh mục chuyên gia', 'name' => 'deleteExpertCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $catModId, 'created_at' => $now, 'updated_at' => $now],

            // Bài viết chuyên gia
            ['title' => 'Xem bài viết chuyên gia', 'name' => 'viewExpertPost', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $postModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo bài viết chuyên gia', 'name' => 'createExpertPost', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $postModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa bài viết chuyên gia', 'name' => 'updateExpertPost', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $postModId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa bài viết chuyên gia', 'name' => 'deleteExpertPost', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $postModId, 'created_at' => $now, 'updated_at' => $now],
        ];

        $permNames = array_column($permissions, 'name');
        DB::table('permissions')->insertOrIgnore($permissions);

        // Gán quyền cho Super Admin
        $superAdminRole = DB::table('roles')->where('name', 'superAdmin')->first();
        if ($superAdminRole) {
            $insertedPerms = DB::table('permissions')->whereIn('name', $permNames)->get();
            $rolePerms = [];
            foreach ($insertedPerms as $perm) {
                $rolePerms[] = [
                    'permission_id' => $perm->id,
                    'role_id' => $superAdminRole->id,
                ];
            }
            DB::table('role_has_permissions')->insertOrIgnore($rolePerms);
        }

        // Reset permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permNames = [
            'viewIntroduction', 'createIntroduction', 'updateIntroduction', 'deleteIntroduction',
            'viewExpert', 'createExpert', 'updateExpert', 'deleteExpert',
            'viewExpertCategory', 'createExpertCategory', 'updateExpertCategory', 'deleteExpertCategory',
            'viewExpertPost', 'createExpertPost', 'updateExpertPost', 'deleteExpertPost',
        ];

        $permIds = DB::table('permissions')->whereIn('name', $permNames)->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('name', $permNames)->delete();
        DB::table('modules')->whereIn('name', [
            'Quản lý Giới thiệu nền tảng',
            'Quản lý Hồ sơ Chuyên gia',
            'Quản lý Chuyên mục Chuyên gia',
            'Quản lý Bài viết Chuyên gia',
        ])->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
