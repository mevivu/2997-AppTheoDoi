<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // 1. Cập nhật các câu hỏi đang liên kết với ID trùng lặp về ID gốc (ID nhỏ nhất của từng type)
        try {
            DB::statement("
                UPDATE questions q
                JOIN question_groups dup ON q.question_group_id = dup.id
                JOIN (
                    SELECT type, MIN(id) as keep_id 
                    FROM question_groups 
                    GROUP BY type
                ) keep ON dup.type = keep.type
                SET q.question_group_id = keep.keep_id
                WHERE dup.id != keep.keep_id
            ");
        } catch (\Throwable $e) {
            // Log or ignore if tables empty
        }

        // 2. Xóa các bản ghi nhóm câu hỏi bị trùng lặp
        try {
            DB::statement("
                DELETE dup FROM question_groups dup
                JOIN (
                    SELECT type, MIN(id) as keep_id 
                    FROM question_groups 
                    GROUP BY type
                ) keep ON dup.type = keep.type
                WHERE dup.id != keep.keep_id
            ");
        } catch (\Throwable $e) {
            // Log or ignore
        }

        // 3. Thêm ràng buộc UNIQUE cho cột type để không bao giờ bị trùng lặp lại
        try {
            Schema::table('question_groups', function (Blueprint $table) {
                $table->unique('type', 'question_groups_type_unique');
            });
        } catch (\Throwable $e) {
            // Bỏ qua nếu index đã tồn tại
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        try {
            Schema::table('question_groups', function (Blueprint $table) {
                $table->dropUnique('question_groups_type_unique');
            });
        } catch (\Throwable $e) {
            // Ignore
        }
    }
};
