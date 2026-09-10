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
        if (!Schema::hasTable('affiliate_histories')) {
            Schema::create('affiliate_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->comment('Người nhận thưởng / sở hữu ví');
                $table->unsignedBigInteger('source_user_id')->nullable()->comment('Người dùng mới tạo ra hoa hồng');
                $table->decimal('amount', 15, 0)->comment('Số tiền biến động (VNĐ)');
                $table->decimal('balance_after', 15, 0)->default(0)->comment('Số dư ví sau biến động');
                $table->string('type', 50)->default('referral_register')->comment('Loại thưởng: referral_register, welcome_register, ...');
                $table->string('description')->nullable()->comment('Diễn giải giao dịch');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('source_user_id')->references('id')->on('users')->onDelete('set null');

                $table->index('user_id');
                $table->index('source_user_id');
                $table->index('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_histories');
    }
};
