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
        if (Schema::hasTable('memo_age_configs')) {
            if (Schema::hasColumn('memo_age_configs', 'max_mistakes') && !Schema::hasColumn('memo_age_configs', 'max_moves')) {
                DB::statement("ALTER TABLE `memo_age_configs` CHANGE COLUMN `max_mistakes` `max_moves` INT NOT NULL DEFAULT 0 COMMENT 'Số lượt mở tối đa (cả đúng và sai) trước khi Game Over (0 = không giới hạn)'");
            } elseif (!Schema::hasColumn('memo_age_configs', 'max_moves')) {
                DB::statement("ALTER TABLE `memo_age_configs` ADD COLUMN `max_moves` INT NOT NULL DEFAULT 0 COMMENT 'Số lượt mở tối đa (cả đúng và sai) trước khi Game Over (0 = không giới hạn)' AFTER `peek_time`");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('memo_age_configs')) {
            if (Schema::hasColumn('memo_age_configs', 'max_moves')) {
                Schema::table('memo_age_configs', function (Blueprint $table) {
                    $table->dropColumn('max_moves');
                });
            }
        }
    }
};
