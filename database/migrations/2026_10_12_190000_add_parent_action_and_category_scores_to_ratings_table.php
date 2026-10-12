<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            if (!Schema::hasColumn('ratings', 'parent_action')) {
                $table->text('parent_action')->nullable()->after('description')->comment('Hành động gợi ý cho bố mẹ dựa trên tổng điểm');
            }
            if (!Schema::hasColumn('ratings', 'category_scores')) {
                $table->json('category_scores')->nullable()->after('parent_action')->comment('Snapshot điểm chi tiết và trạng thái yếu của từng danh mục');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            if (Schema::hasColumn('ratings', 'parent_action')) {
                $table->dropColumn('parent_action');
            }
            if (Schema::hasColumn('ratings', 'category_scores')) {
                $table->dropColumn('category_scores');
            }
        });
    }
};
