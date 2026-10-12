<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Đảm bảo content và image cho phép nullable (không crash khi tạo nhật ký không ảnh)
        // Dùng raw SQL để không bắt buộc cài doctrine/dbal trên Laravel 9
        try {
            DB::statement('ALTER TABLE journals MODIFY content TEXT NULL, MODIFY image TEXT NULL');
        } catch (\Throwable $e) {
            // Trường hợp DB khác MySQL hoặc đã nullable
        }

        Schema::table('journals', function (Blueprint $table) {
            if (!Schema::hasColumn('journals', 'milestone_id')) {
                $table->unsignedBigInteger('milestone_id')->nullable()->after('child_id');
                $table->foreign('milestone_id')->references('id')->on('journal_milestones')->nullOnDelete();
                $table->unique(['child_id', 'milestone_id'], 'journals_child_id_milestone_id_unique');
            }

            // Composite index cho truy vấn danh sách theo ngày sự kiện
            $table->index(['child_id', 'type', 'created_at'], 'journals_child_id_type_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            if (Schema::hasColumn('journals', 'milestone_id')) {
                $table->dropForeign(['milestone_id']);
                $table->dropUnique('journals_child_id_milestone_id_unique');
                $table->dropColumn('milestone_id');
            }
            $table->dropIndex('journals_child_id_type_created_at_index');
        });
    }
};
