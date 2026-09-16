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
        // 1. memo_themes
        if (!Schema::hasTable('memo_themes')) {
            Schema::create('memo_themes', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->comment('Tên chủ đề');
                $table->string('code', 50)->unique()->comment('Mã chủ đề (vehicles, flowers, numbers, flags)');
                $table->string('icon', 255)->nullable()->comment('Ảnh đại diện chủ đề');
                $table->string('card_back', 255)->nullable()->comment('Ảnh mặt sau thẻ mặc định');
                $table->text('description')->nullable()->comment('Mô tả chủ đề');
                $table->integer('position')->default(0)->comment('Thứ tự sắp xếp');
                $table->enum('status', ['active', 'draft', 'deleted'])->default('active')->comment('Trạng thái');
                $table->timestamps();
            });
        }

        // 2. memo_cards
        if (!Schema::hasTable('memo_cards')) {
            Schema::create('memo_cards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('memo_theme_id')->constrained('memo_themes')->onDelete('cascade');
                $table->string('name', 191)->comment('Tên thẻ');
                $table->string('image', 255)->comment('Ảnh mặt trước thẻ');
                $table->string('audio', 255)->nullable()->comment('File âm thanh phát âm');
                $table->enum('status', ['active', 'draft', 'deleted'])->default('active')->comment('Trạng thái');
                $table->timestamps();
            });
        }

        // 3. memo_age_configs
        if (!Schema::hasTable('memo_age_configs')) {
            Schema::create('memo_age_configs', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->comment('Tên mức độ');
                $table->integer('min_age')->comment('Tuổi tối thiểu');
                $table->integer('max_age')->comment('Tuổi tối đa');
                $table->integer('rows')->comment('Số hàng');
                $table->integer('columns')->comment('Số cột');
                $table->integer('total_cards')->comment('Tổng số thẻ = rows * columns');
                $table->integer('pairs_count')->comment('Số cặp thẻ = total_cards / 2');
                $table->integer('total_duration')->default(180)->comment('Thời gian bài test (giây)');
                $table->integer('total_rounds')->default(3)->comment('Số lượt game trong bài test');
                $table->integer('peek_time')->default(3)->comment('Thời gian xem trước ban đầu (giây)');
                $table->enum('status', ['active', 'draft', 'deleted'])->default('active')->comment('Trạng thái');
                $table->timestamps();
            });
        }

        // 4. memo_ratings
        if (!Schema::hasTable('memo_ratings')) {
            Schema::create('memo_ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('child_id')->nullable()->constrained('children')->onDelete('cascade');
                $table->foreignId('memo_theme_id')->nullable()->constrained('memo_themes')->onDelete('set null');
                $table->foreignId('memo_age_config_id')->nullable()->constrained('memo_age_configs')->onDelete('set null');
                $table->integer('age')->nullable()->comment('Tuổi của bé tại thời điểm test');
                $table->integer('total_duration_spent')->default(0)->comment('Thời gian đã dùng (giây)');
                $table->integer('total_pairs_matched')->default(0)->comment('Tổng số cặp ghép đúng');
                $table->integer('total_mistakes')->default(0)->comment('Tổng số lần lật sai');
                $table->double('score', 8, 2)->default(0)->comment('Điểm số tổng hợp (0 - 100)');
                $table->string('evaluation_label', 191)->nullable()->comment('Xếp loại đánh giá');
                $table->text('feedback')->nullable()->comment('Nhận xét chuyên môn');
                $table->enum('status', ['pending', 'completed'])->default('completed')->comment('Trạng thái');
                $table->timestamps();
            });
        }

        // 5. memo_rating_rounds
        if (!Schema::hasTable('memo_rating_rounds')) {
            Schema::create('memo_rating_rounds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('memo_rating_id')->constrained('memo_ratings')->onDelete('cascade');
                $table->integer('round_number')->comment('Lượt thứ mấy (1, 2, 3)');
                $table->integer('duration_spent')->default(0)->comment('Thời gian của lượt (giây)');
                $table->integer('pairs_matched')->default(0)->comment('Số cặp đúng');
                $table->integer('mistakes')->default(0)->comment('Số lần sai');
                $table->double('score', 8, 2)->default(0)->comment('Điểm lượt này');
                $table->timestamps();
            });
        }

        // Seed 4 chủ đề mặc định
        $themes = [
            [
                'name' => 'Phương tiện giao thông (Xe)',
                'code' => 'vehicles',
                'description' => 'Bài test trí nhớ với các loại xe, phương tiện giao thông quen thuộc và sinh động.',
                'position' => 1,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Các loài hoa (Hoa)',
                'code' => 'flowers',
                'description' => 'Bài test trí nhớ với các loài hoa rực rỡ, giúp bé rèn luyện khả năng ghi nhớ màu sắc và hình ảnh.',
                'position' => 2,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chữ số vui nhộn (Số)',
                'code' => 'numbers',
                'description' => 'Bài test trí nhớ với các chữ số từ 1 đến 10, kết hợp làm quen và rèn luyện tư duy số học.',
                'position' => 3,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quốc kỳ các nước (Cờ)',
                'code' => 'flags',
                'description' => 'Bài test trí nhớ với quốc kỳ các quốc gia trên thế giới, mở rộng tầm nhìn và nhận biết biểu tượng.',
                'position' => 4,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($themes as $theme) {
            $exists = DB::table('memo_themes')->where('code', $theme['code'])->exists();
            if (!$exists) {
                DB::table('memo_themes')->insert($theme);
            }
        }

        // Seed 4 cấu hình độ tuổi chuẩn
        $ageConfigs = [
            [
                'name' => 'Lứa tuổi 3 - 5 tuổi (Khởi động)',
                'min_age' => 3,
                'max_age' => 5,
                'rows' => 2,
                'columns' => 3,
                'total_cards' => 6,
                'pairs_count' => 3,
                'total_duration' => 180,
                'total_rounds' => 3,
                'peek_time' => 3,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lứa tuổi 6 - 8 tuổi (Cơ bản)',
                'min_age' => 6,
                'max_age' => 8,
                'rows' => 3,
                'columns' => 4,
                'total_cards' => 12,
                'pairs_count' => 6,
                'total_duration' => 180,
                'total_rounds' => 3,
                'peek_time' => 3,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lứa tuổi 9 - 11 tuổi (Nâng cao)',
                'min_age' => 9,
                'max_age' => 11,
                'rows' => 4,
                'columns' => 4,
                'total_cards' => 16,
                'pairs_count' => 8,
                'total_duration' => 180,
                'total_rounds' => 3,
                'peek_time' => 3,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lứa tuổi 12+ tuổi (Thử thách)',
                'min_age' => 12,
                'max_age' => 18,
                'rows' => 4,
                'columns' => 5,
                'total_cards' => 20,
                'pairs_count' => 10,
                'total_duration' => 180,
                'total_rounds' => 3,
                'peek_time' => 3,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($ageConfigs as $cfg) {
            $exists = DB::table('memo_age_configs')
                ->where('min_age', $cfg['min_age'])
                ->where('max_age', $cfg['max_age'])
                ->exists();
            if (!$exists) {
                DB::table('memo_age_configs')->insert($cfg);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('memo_rating_rounds');
        Schema::dropIfExists('memo_ratings');
        Schema::dropIfExists('memo_age_configs');
        Schema::dropIfExists('memo_cards');
        Schema::dropIfExists('memo_themes');
    }
};
