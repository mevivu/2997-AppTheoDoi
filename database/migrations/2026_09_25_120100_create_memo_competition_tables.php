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
        // 1. memo_competitions
        if (!Schema::hasTable('memo_competitions')) {
            Schema::create('memo_competitions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->comment('Tên giải đấu');
                $table->string('slug', 255)->nullable()->unique()->comment('Slug URL');
                $table->text('description')->nullable()->comment('Mô tả, thể lệ giải đấu');
                $table->string('banner_image', 500)->nullable()->comment('Ảnh banner giải đấu');
                $table->dateTime('start_at')->comment('Thời gian bắt đầu');
                $table->dateTime('end_at')->comment('Thời gian kết thúc');
                $table->foreignId('memo_age_config_id')->constrained('memo_age_configs')->comment('Cấu hình lưới (thường 5x6)');
                $table->tinyInteger('total_games')->default(4)->comment('Số game trong 1 lượt thi');
                $table->integer('peek_time_override')->default(0)->comment('Thời gian xem trước (0 = không xem)');
                $table->tinyInteger('max_attempts')->default(1)->comment('Số lần thi tối đa mỗi bé (0 hoặc null: không giới hạn)');
                $table->boolean('must_win_all')->default(true)->comment('Phải thắng toàn bộ game mới tính thành tích');
                $table->timestamp('ranking_calculated_at')->nullable()->comment('Thời điểm chốt tính bảng xếp hạng');
                $table->enum('status', ['draft', 'upcoming', 'active', 'ended', 'cancelled'])->default('draft')->comment('Trạng thái');
                $table->foreignId('created_by')->nullable()->constrained('admins')->onDelete('set null')->comment('Admin tạo giải đấu');
                $table->timestamps();

                $table->index(['status', 'start_at', 'end_at'], 'idx_comp_status_dates');
            });
        }

        // 2. memo_competition_themes
        if (!Schema::hasTable('memo_competition_themes')) {
            Schema::create('memo_competition_themes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('memo_competition_id')->constrained('memo_competitions')->onDelete('cascade');
                $table->foreignId('memo_theme_id')->constrained('memo_themes')->onDelete('cascade');
                $table->tinyInteger('game_order')->comment('Thứ tự game (1, 2, 3, 4)');
                $table->timestamps();

                $table->unique(['memo_competition_id', 'game_order'], 'uniq_comp_order');
                $table->unique(['memo_competition_id', 'memo_theme_id'], 'uniq_comp_theme');
            });
        }

        // 3. memo_competition_entries
        if (!Schema::hasTable('memo_competition_entries')) {
            Schema::create('memo_competition_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('memo_competition_id')->constrained('memo_competitions')->onDelete('cascade');
                $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
                $table->tinyInteger('attempt_number')->default(1)->comment('Lần thi thứ mấy');
                $table->integer('total_time')->default(0)->comment('Tổng thời gian hoàn thành (giây) - tiêu chí xếp hạng chính');
                $table->integer('total_moves')->default(0)->comment('Tổng số lần lật thẻ - tiêu chí phụ');
                $table->integer('total_mistakes')->default(0)->comment('Tổng số lần lật sai');
                $table->integer('total_pairs_matched')->default(0)->comment('Tổng số cặp ghép đúng');
                $table->tinyInteger('games_won')->default(0)->comment('Số game thắng');
                $table->enum('status', ['in_progress', 'completed', 'abandoned', 'disqualified'])->default('in_progress');
                $table->boolean('is_valid')->default(false)->comment('Lượt thi hợp lệ (thắng hết và không vi phạm)');
                $table->integer('ranking')->nullable()->comment('Xếp hạng cuối cùng');
                $table->dateTime('started_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['memo_competition_id', 'child_id', 'attempt_number'], 'uniq_comp_child_attempt');
                $table->index(['memo_competition_id', 'is_valid', 'total_time', 'total_moves'], 'idx_comp_ranking');
            });
        }

        // 4. memo_competition_rounds
        if (!Schema::hasTable('memo_competition_rounds')) {
            Schema::create('memo_competition_rounds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('memo_competition_entry_id')->constrained('memo_competition_entries')->onDelete('cascade');
                $table->foreignId('memo_theme_id')->nullable()->constrained('memo_themes')->onDelete('set null');
                $table->tinyInteger('game_number')->comment('Game thứ mấy (1, 2, 3, 4)');
                $table->integer('duration_spent')->default(0)->comment('Thời gian hoàn thành game này (giây)');
                $table->integer('pairs_matched')->default(0)->comment('Số cặp ghép đúng');
                $table->integer('total_moves')->default(0)->comment('Số lần lật thẻ');
                $table->integer('mistakes')->default(0)->comment('Số lần lật sai');
                $table->boolean('is_won')->default(false)->comment('Đã thắng game này chưa');
                $table->dateTime('started_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['memo_competition_entry_id', 'game_number'], 'uniq_entry_game');
            });
        }

        // 5. Seed cấu hình 5x6 chuẩn cho giải đấu vào memo_age_configs nếu chưa có
        $hasTournamentConfig = DB::table('memo_age_configs')
            ->where('rows', 5)
            ->where('columns', 6)
            ->exists();

        if (!$hasTournamentConfig) {
            DB::table('memo_age_configs')->insert([
                'name' => 'Giải đấu (5×6 - Thử thách đỉnh cao)',
                'min_age' => 1,
                'max_age' => 99,
                'rows' => 5,
                'columns' => 6,
                'total_cards' => 30,
                'pairs_count' => 15,
                'total_duration' => 300,
                'total_rounds' => 4,
                'peek_time' => 0,
                'max_moves' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('memo_competition_rounds');
        Schema::dropIfExists('memo_competition_entries');
        Schema::dropIfExists('memo_competition_themes');
        Schema::dropIfExists('memo_competitions');
    }
};
