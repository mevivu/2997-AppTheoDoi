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
            ['name' => 'Quản lý Danh mục Bài học', 'description' => 'Quản lý danh mục bài học theo độ tuổi và 5 trụ cột giáo dục', 'status' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Quản lý Bài học Giáo dục', 'description' => 'Quản lý bài học giáo dục kèm đa video YouTube & Cloudflare R2', 'status' => 2, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($modules as $mod) {
            DB::table('modules')->insert($mod);
        }

        $lessonCatId = DB::table('modules')->where('name', 'Quản lý Danh mục Bài học')->value('id');
        $lessonId = DB::table('modules')->where('name', 'Quản lý Bài học Giáo dục')->value('id');

        $permissions = [
            // Danh mục Bài học
            ['title' => 'Xem danh mục bài học', 'name' => 'viewLessonCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $lessonCatId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo danh mục bài học', 'name' => 'createLessonCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $lessonCatId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa danh mục bài học', 'name' => 'updateLessonCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $lessonCatId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa danh mục bài học', 'name' => 'deleteLessonCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $lessonCatId, 'created_at' => $now, 'updated_at' => $now],

            // Bài học
            ['title' => 'Xem bài học giáo dục', 'name' => 'viewLesson', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $lessonId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo bài học giáo dục', 'name' => 'createLesson', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $lessonId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa bài học giáo dục', 'name' => 'updateLesson', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $lessonId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa bài học giáo dục', 'name' => 'deleteLesson', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $lessonId, 'created_at' => $now, 'updated_at' => $now],
        ];

        $permNames = array_column($permissions, 'name');
        DB::table('permissions')->insert($permissions);

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
            'viewLessonCategory', 'createLessonCategory', 'updateLessonCategory', 'deleteLessonCategory',
            'viewLesson', 'createLesson', 'updateLesson', 'deleteLesson',
        ];

        $permIds = DB::table('permissions')->whereIn('name', $permNames)->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('name', $permNames)->delete();
        DB::table('modules')->whereIn('name', [
            'Quản lý Danh mục Bài học',
            'Quản lý Bài học Giáo dục',
        ])->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
