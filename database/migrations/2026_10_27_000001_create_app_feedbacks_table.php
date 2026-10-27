<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('app_feedbacks')) {
            Schema::create('app_feedbacks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index()->comment('User ID lấy từ tài khoản Chăm Con 360');
                $table->string('email', 255)->nullable()->comment('Email người dùng');
                $table->unsignedTinyInteger('rating')->comment('Câu 1: Mức độ hài lòng (1-5 sao)');
                $table->string('favorite_feature', 150)->comment('Câu 2: Tính năng hài lòng nhất');
                $table->string('improvement_feedback', 500)->comment('Câu 3: Mong muốn cải thiện (tối đa 500 ký tự)');
                $table->string('status', 30)->default('pending')->index()->comment('pending: Chờ duyệt, approved: Đã duyệt, vip_granted: Đã cấp VIP, rejected: Từ chối');
                $table->unsignedBigInteger('granted_package_id')->nullable()->comment('ID gói VIP đã cấp từ danh sách gói hoạt động');
                $table->timestamp('vip_granted_at')->nullable()->index()->comment('Thời điểm cấp gói VIP (chống cấp trùng)');
                $table->unsignedBigInteger('granted_by_admin_id')->nullable()->comment('ID Admin thực hiện duyệt/cấp VIP');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_feedbacks');
    }
};
