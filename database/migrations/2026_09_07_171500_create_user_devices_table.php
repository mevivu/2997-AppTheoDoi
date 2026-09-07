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
    public function up(): void
    {
        if (!Schema::hasTable('user_devices')) {
            Schema::create('user_devices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('device_id', 191)->index()->comment('Định danh thiết bị duy nhất (UUID/Hardware ID hoặc Token)');
                $table->string('device_name', 191)->nullable()->comment('Tên dòng máy (iPhone 15, Samsung S24...)');
                $table->text('device_token')->nullable()->comment('FCM Push Notification Token');
                $table->string('ip_address', 45)->nullable()->comment('Địa chỉ IP đăng nhập');
                $table->boolean('is_active')->default(true)->index()->comment('true = đang liên kết hoạt động, false = đã giải phóng');
                $table->timestamp('last_active_at')->nullable()->comment('Thời điểm hoạt động gần nhất');
                $table->timestamps();

                $table->index(['user_id', 'device_id']);
                $table->index(['user_id', 'is_active']);
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
        Schema::dropIfExists('user_devices');
    }
};
