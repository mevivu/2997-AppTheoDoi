<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_rank_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('period', 7)->comment('Kỳ đánh giá (Định dạng YYYY-MM, ví dụ: 2026-10)');

            // 1. Số liệu thô tích lũy trong tháng
            $table->unsignedInteger('usage_minutes')->default(0)->comment('Tổng thời gian sử dụng app trong tháng (phút)');
            $table->unsignedTinyInteger('active_days')->default(0)->comment('Số ngày có hoạt động vào app trong tháng');
            $table->unsignedSmallInteger('session_count')->default(0)->comment('Tổng số phiên mở app trong tháng');
            $table->unsignedSmallInteger('assessment_count')->default(0)->comment('Tổng số bài đánh giá đã thực hiện cho con trong tháng');
            $table->decimal('child_score_avg', 5, 2)->default(0)->comment('Điểm trung bình các chỉ số của con (thang chuẩn hóa 0 - 100)');

            // 2. Điểm số thành phần đã chuẩn hóa về thang 0 - 100 theo mục tiêu tháng
            $table->decimal('score_usage', 5, 2)->default(0)->comment('Điểm thành phần: Thời gian sử dụng');
            $table->decimal('score_frequency', 5, 2)->default(0)->comment('Điểm thành phần: Tần suất');
            $table->decimal('score_assessment', 5, 2)->default(0)->comment('Điểm thành phần: Bài đánh giá cho con');
            $table->decimal('score_child', 5, 2)->default(0)->comment('Điểm thành phần: Chỉ số phát triển của con');

            // 3. Điểm tổng hợp và Xếp hạng
            $table->decimal('total_points', 5, 2)->default(0)->comment('Điểm tổng hợp chung cuộc');
            $table->unsignedTinyInteger('rank')->default(0)->comment('Cấp bậc đạt được (Enum ParentRank: 0=Mới, 1=Đồng, 2=Bạc, 3=Vàng, 4=Kim Cương)');
            $table->unsignedInteger('position')->nullable()->comment('Vị trí xếp hạng thứ tự trong kỳ (Leaderboard position)');

            // 4. Trạng thái & Audit
            $table->boolean('is_final')->default(false)->comment('Đã chốt sổ kỳ này hay chưa (tháng trước chốt = true, tháng hiện tại = false)');
            $table->json('config_snapshot')->nullable()->comment('Bản sao lưu cấu hình trọng số & mục tiêu tại thời điểm tính');
            $table->timestamp('calculated_at')->nullable()->comment('Thời điểm tính toán bản ghi snapshot');
            $table->timestamps();

            // Ràng buộc & Chỉ mục
            $table->unique(['user_id', 'period'], 'parent_rank_user_period_unique');
            $table->index(['period', 'rank']);
            $table->index(['period', 'total_points']);
            $table->index(['period', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parent_rank_snapshots');
    }
};
