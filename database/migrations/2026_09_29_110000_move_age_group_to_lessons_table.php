<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cập nhật các danh mục đang có pillar 'thai_giao' chuyển sang 'iq' (Trí tuệ / Học tập)
        DB::table('lesson_categories')
            ->where('pillar', 'thai_giao')
            ->update(['pillar' => 'iq']);

        // 2. Thêm cột age_group_id vào bảng lessons
        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('age_group_id')
                ->nullable()
                ->after('lesson_category_id')
                ->constrained('age_groups')
                ->cascadeOnDelete();

            $table->index(['age_group_id', 'status']);
        });

        // 3. Sao chép dữ liệu age_group_id từ lesson_categories sang lessons
        DB::statement("
            UPDATE lessons l
            INNER JOIN lesson_categories lc ON l.lesson_category_id = lc.id
            SET l.age_group_id = lc.age_group_id
            WHERE lc.age_group_id IS NOT NULL
        ");

        // 4. Xóa khóa ngoại, index và cột age_group_id khỏi bảng lesson_categories
        Schema::table('lesson_categories', function (Blueprint $table) {
            $table->dropForeign(['age_group_id']);
            $table->dropIndex(['age_group_id', 'pillar']);
            $table->dropColumn('age_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Thêm lại age_group_id vào bảng lesson_categories
        Schema::table('lesson_categories', function (Blueprint $table) {
            $table->foreignId('age_group_id')
                ->nullable()
                ->after('id')
                ->constrained('age_groups')
                ->cascadeOnDelete();

            $table->index(['age_group_id', 'pillar']);
        });

        // 2. Khôi phục dữ liệu age_group_id vào lesson_categories từ lessons
        DB::statement("
            UPDATE lesson_categories lc
            INNER JOIN lessons l ON l.lesson_category_id = lc.id
            SET lc.age_group_id = l.age_group_id
            WHERE l.age_group_id IS NOT NULL
        ");

        // 3. Xóa age_group_id khỏi bảng lessons
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['age_group_id']);
            $table->dropIndex(['age_group_id', 'status']);
            $table->dropColumn('age_group_id');
        });
    }
};
