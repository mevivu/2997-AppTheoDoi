<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        $permId = DB::table('permissions')->insertGetId([
            'title' => 'Debug & Tính lại Học bạ',
            'name' => 'debugReportCard',
            'type' => 'admin',
            'guard_name' => 'admin',
            'module_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $superAdminRole = DB::table('roles')
            ->where('name', 'superAdmin')
            ->where('guard_name', 'admin')
            ->first();

        if ($superAdminRole && $permId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permId,
                'role_id' => $superAdminRole->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $perm = DB::table('permissions')->where('name', 'debugReportCard')->first();
        if ($perm) {
            DB::table('role_has_permissions')->where('permission_id', $perm->id)->delete();
            DB::table('permissions')->where('id', $perm->id)->delete();
        }
    }
};
