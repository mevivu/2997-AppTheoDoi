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
            ['name' => 'Quản lý Nhóm tuổi', 'description' => 'Quản lý các nhóm độ tuổi cho giáo dục và bài tập', 'status' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Quản lý Danh mục Video', 'description' => 'Quản lý danh mục video giáo dục theo nhóm tuổi', 'status' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Quản lý Video Giáo dục', 'description' => 'Quản lý nội dung video YouTube và quyền Free/VIP', 'status' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Quản lý Danh mục Bài tập', 'description' => 'Quản lý danh mục bài tập theo chủ đề PQ, IQ, EQ, AQ, Thai giáo', 'status' => 2, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($modules as $mod) {
            DB::table('modules')->insert($mod);
        }

        $ageGroupId = DB::table('modules')->where('name', 'Quản lý Nhóm tuổi')->value('id');
        $videoCatId = DB::table('modules')->where('name', 'Quản lý Danh mục Video')->value('id');
        $videoId = DB::table('modules')->where('name', 'Quản lý Video Giáo dục')->value('id');
        $exerciseCatId = DB::table('modules')->where('name', 'Quản lý Danh mục Bài tập')->value('id');

        $permissions = [
            // Nhóm tuổi
            ['title' => 'Xem nhóm tuổi', 'name' => 'viewAgeGroup', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $ageGroupId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo nhóm tuổi', 'name' => 'createAgeGroup', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $ageGroupId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa nhóm tuổi', 'name' => 'updateAgeGroup', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $ageGroupId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa nhóm tuổi', 'name' => 'deleteAgeGroup', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $ageGroupId, 'created_at' => $now, 'updated_at' => $now],

            // Danh mục Video
            ['title' => 'Xem danh mục video', 'name' => 'viewVideoCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $videoCatId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo danh mục video', 'name' => 'createVideoCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $videoCatId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa danh mục video', 'name' => 'updateVideoCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $videoCatId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa danh mục video', 'name' => 'deleteVideoCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $videoCatId, 'created_at' => $now, 'updated_at' => $now],

            // Video
            ['title' => 'Xem video giáo dục', 'name' => 'viewVideo', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $videoId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo video giáo dục', 'name' => 'createVideo', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $videoId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa video giáo dục', 'name' => 'updateVideo', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $videoId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa video giáo dục', 'name' => 'deleteVideo', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $videoId, 'created_at' => $now, 'updated_at' => $now],

            // Danh mục Bài tập
            ['title' => 'Xem danh mục bài tập', 'name' => 'viewExerciseCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $exerciseCatId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo danh mục bài tập', 'name' => 'createExerciseCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $exerciseCatId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa danh mục bài tập', 'name' => 'updateExerciseCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $exerciseCatId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa danh mục bài tập', 'name' => 'deleteExerciseCategory', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $exerciseCatId, 'created_at' => $now, 'updated_at' => $now],
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
            'viewAgeGroup', 'createAgeGroup', 'updateAgeGroup', 'deleteAgeGroup',
            'viewVideoCategory', 'createVideoCategory', 'updateVideoCategory', 'deleteVideoCategory',
            'viewVideo', 'createVideo', 'updateVideo', 'deleteVideo',
            'viewExerciseCategory', 'createExerciseCategory', 'updateExerciseCategory', 'deleteExerciseCategory',
        ];

        $permIds = DB::table('permissions')->whereIn('name', $permNames)->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('name', $permNames)->delete();
        DB::table('modules')->whereIn('name', [
            'Quản lý Nhóm tuổi',
            'Quản lý Danh mục Video',
            'Quản lý Video Giáo dục',
            'Quản lý Danh mục Bài tập',
        ])->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
