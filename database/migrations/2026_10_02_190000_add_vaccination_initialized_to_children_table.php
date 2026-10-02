<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Thêm cột vaccination_initialized vào bảng children.
     * Mục đích: Đánh dấu hồ sơ con đã kích hoạt sổ tiêm chủng hay chưa.
     * - 0 = chưa kích hoạt (mặc định cho hồ sơ mới)
     * - 1 = đã kích hoạt
     *
     * Đồng thời cập nhật tất cả hồ sơ con đã có dữ liệu tiêm chủng
     * thành vaccination_initialized = 1 để không ảnh hưởng user hiện tại.
     */
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->boolean('vaccination_initialized')
                ->default(false)
                ->after('is_born')
                ->comment('Đánh dấu đã kích hoạt sổ tiêm chủng: 0=chưa, 1=đã kích hoạt');
        });

        // Cập nhật tất cả hồ sơ con đã có vaccination schedules → đánh dấu đã khởi tạo
        // Điều này đảm bảo user hiện tại không bị ảnh hưởng
        DB::statement("
            UPDATE children 
            SET vaccination_initialized = 1 
            WHERE id IN (
                SELECT DISTINCT child_id 
                FROM vaccination_schedules 
                WHERE child_id IS NOT NULL
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn('vaccination_initialized');
        });
    }
};
