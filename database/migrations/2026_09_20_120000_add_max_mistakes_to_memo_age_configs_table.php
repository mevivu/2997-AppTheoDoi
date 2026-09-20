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
        if (Schema::hasTable('memo_age_configs')) {
            Schema::table('memo_age_configs', function (Blueprint $table) {
                if (!Schema::hasColumn('memo_age_configs', 'max_mistakes')) {
                    $table->integer('max_mistakes')->default(0)->after('peek_time')->comment('Số lần lật sai tối đa trước khi Game Over (0 = không giới hạn)');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('memo_age_configs')) {
            Schema::table('memo_age_configs', function (Blueprint $table) {
                if (Schema::hasColumn('memo_age_configs', 'max_mistakes')) {
                    $table->dropColumn('max_mistakes');
                }
            });
        }
    }
};
