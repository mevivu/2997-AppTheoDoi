<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            if (!Schema::hasColumn('ratings', 'version')) {
                $table->string('version', 10)->default('v1')->after('status')->comment('Phiên bản bài test (v1, v2)');
            }
            if (!Schema::hasColumn('ratings', 'game_score')) {
                $table->integer('game_score')->nullable()->after('version')->comment('Điểm memo game (0 hoặc 1)');
            }
            if (!Schema::hasColumn('ratings', 'game_duration_spent')) {
                $table->integer('game_duration_spent')->nullable()->after('game_score')->comment('Thời gian chơi game (giây)');
            }
            if (!Schema::hasColumn('ratings', 'game_pairs_matched')) {
                $table->integer('game_pairs_matched')->nullable()->after('game_duration_spent')->comment('Số cặp ghép đúng');
            }
            if (!Schema::hasColumn('ratings', 'game_mistakes')) {
                $table->integer('game_mistakes')->nullable()->after('game_pairs_matched')->comment('Số lần lật sai');
            }
            if (!Schema::hasColumn('ratings', 'memo_theme_id')) {
                $table->foreignId('memo_theme_id')->nullable()->after('game_mistakes')->constrained('memo_themes')->nullOnDelete();
            }
            if (!Schema::hasColumn('ratings', 'memo_age_config_id')) {
                $table->foreignId('memo_age_config_id')->nullable()->after('memo_theme_id')->constrained('memo_age_configs')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            if (Schema::hasColumn('ratings', 'memo_age_config_id')) {
                $table->dropForeign(['memo_age_config_id']);
                $table->dropColumn('memo_age_config_id');
            }
            if (Schema::hasColumn('ratings', 'memo_theme_id')) {
                $table->dropForeign(['memo_theme_id']);
                $table->dropColumn('memo_theme_id');
            }
            $columns = ['version', 'game_score', 'game_duration_spent', 'game_pairs_matched', 'game_mistakes'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('ratings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
