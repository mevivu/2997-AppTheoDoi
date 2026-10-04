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
        Schema::create('user_daily_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('activity_date')->comment('Ngày hoạt động (theo timezone Asia/Ho_Chi_Minh)');
            $table->unsignedInteger('active_seconds')->default(0)->comment('Tổng thời gian hoạt động thực tế trong ngày (giây)');
            $table->unsignedSmallInteger('session_count')->default(0)->comment('Số phiên mở ứng dụng trong ngày');
            $table->timestamp('last_heartbeat_at')->nullable()->comment('Thời điểm gửi tín hiệu heartbeat gần nhất');
            $table->timestamps();

            // Mỗi user chỉ có 1 bản ghi duy nhất cho mỗi ngày
            $table->unique(['user_id', 'activity_date'], 'user_daily_activity_unique');
            $table->index('activity_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_daily_activities');
    }
};
