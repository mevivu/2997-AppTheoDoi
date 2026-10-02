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

        $module = [
            'name' => 'Quản lý Kiến thức chăm con',
            'description' => 'Quản lý các bài viết kiến thức và cẩm nang chăm sóc con',
            'status' => 2,
            'created_at' => $now,
            'updated_at' => $now
        ];

        DB::table('modules')->insertOrIgnore($module);

        $moduleId = DB::table('modules')->where('name', 'Quản lý Kiến thức chăm con')->value('id');

        $permissions = [
            ['title' => 'Xem kiến thức chăm con', 'name' => 'viewKnowledgePost', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $moduleId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo kiến thức chăm con', 'name' => 'createKnowledgePost', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $moduleId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa kiến thức chăm con', 'name' => 'updateKnowledgePost', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $moduleId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa kiến thức chăm con', 'name' => 'deleteKnowledgePost', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $moduleId, 'created_at' => $now, 'updated_at' => $now],
        ];

        $permNames = array_column($permissions, 'name');
        DB::table('permissions')->insertOrIgnore($permissions);

        // Gán quyền cho các role admin (superAdmin hoặc admin)
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

        // Reset permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permNames = [
            'viewKnowledgePost', 'createKnowledgePost', 'updateKnowledgePost', 'deleteKnowledgePost',
        ];

        $permIds = DB::table('permissions')->whereIn('name', $permNames)->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('name', $permNames)->delete();
        DB::table('modules')->where('name', 'Quản lý Kiến thức chăm con')->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
