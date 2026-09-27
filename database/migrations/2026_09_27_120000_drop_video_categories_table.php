<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Gỡ bỏ khóa ngoại và cột video_category_id trên bảng videos (nếu có)
        if (Schema::hasTable('videos')) {
            try {
                Schema::table('videos', function (Blueprint $table) {
                    $table->dropForeign(['video_category_id']);
                });
            } catch (\Throwable $e) {
                // Ignore if foreign key doesn't exist
            }

            if (Schema::hasColumn('videos', 'video_category_id')) {
                Schema::table('videos', function (Blueprint $table) {
                    $table->dropColumn('video_category_id');
                });
            }
        }

        // 2. Xóa bảng video_categories
        Schema::dropIfExists('video_categories');

        Schema::enableForeignKeyConstraints();

        // 3. Dọn dẹp permissions và module Quản lý Danh mục Video
        $permNames = ['viewVideoCategory', 'createVideoCategory', 'updateVideoCategory', 'deleteVideoCategory'];
        $permIds = DB::table('permissions')->whereIn('name', $permNames)->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('name', $permNames)->delete();
        DB::table('modules')->where('name', 'Quản lý Danh mục Video')->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không khôi phục
    }
};
