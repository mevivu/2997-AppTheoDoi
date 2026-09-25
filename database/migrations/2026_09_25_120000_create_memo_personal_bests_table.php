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
        if (!Schema::hasTable('memo_personal_bests')) {
            Schema::create('memo_personal_bests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
                $table->foreignId('memo_age_config_id')->constrained('memo_age_configs')->onDelete('cascade');
                $table->integer('best_time')->comment('Thời gian hoàn thành nhanh nhất (giây)');
                $table->integer('best_moves')->default(0)->comment('Số lượt lật tương ứng ván best_time');
                $table->integer('best_mistakes')->default(0)->comment('Số lần lật sai tương ứng ván best_time');
                $table->double('best_score', 8, 2)->default(0)->comment('Điểm số cao nhất');
                $table->foreignId('memo_rating_id')->nullable()->constrained('memo_ratings')->onDelete('set null')->comment('Ván game tạo ra thành tích này');
                $table->foreignId('memo_theme_id')->nullable()->constrained('memo_themes')->onDelete('set null')->comment('Chủ đề của ván tốt nhất');
                $table->integer('total_games_played')->default(0)->comment('Tổng số ván đã chơi ở level này');
                $table->integer('total_wins')->default(0)->comment('Tổng số ván thắng ở level này');
                $table->timestamp('achieved_at')->nullable()->comment('Thời điểm đạt kỷ lục');
                $table->timestamps();

                $table->unique(['child_id', 'memo_age_config_id'], 'unique_child_memo_level');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('memo_personal_bests');
    }
};
