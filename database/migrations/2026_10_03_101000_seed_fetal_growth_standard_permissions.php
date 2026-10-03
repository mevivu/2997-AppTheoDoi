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
            'name' => 'Quản lý Chuẩn phát triển thai nhi',
            'description' => 'Quản lý quy chuẩn chiều dài (cm), cân nặng (g), chu vi đầu thai nhi theo tuần',
            'status' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('modules')->insertOrIgnore($module);
        $modId = DB::table('modules')->where('name', 'Quản lý Chuẩn phát triển thai nhi')->value('id');

        $permissions = [
            ['title' => 'Xem chuẩn phát triển thai nhi', 'name' => 'viewFetalGrowthStandard', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $modId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tạo chuẩn phát triển thai nhi', 'name' => 'createFetalGrowthStandard', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $modId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sửa chuẩn phát triển thai nhi', 'name' => 'updateFetalGrowthStandard', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $modId, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xóa chuẩn phát triển thai nhi', 'name' => 'deleteFetalGrowthStandard', 'type' => 'admin', 'guard_name' => 'admin', 'module_id' => $modId, 'created_at' => $now, 'updated_at' => $now],
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
            'viewFetalGrowthStandard',
            'createFetalGrowthStandard',
            'updateFetalGrowthStandard',
            'deleteFetalGrowthStandard',
        ];

        $permIds = DB::table('permissions')->whereIn('name', $permNames)->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('name', $permNames)->delete();
        DB::table('modules')->where('name', 'Quản lý Chuẩn phát triển thai nhi')->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
