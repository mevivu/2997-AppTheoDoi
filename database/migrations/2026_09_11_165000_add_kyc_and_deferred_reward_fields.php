<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Bổ sung các cột phục vụ:
     * 1. KYC xác minh CCCD/MST cho đối tác Affiliate trước khi rút tiền
     * 2. Đánh dấu user mới chưa hoàn thành hồ sơ con (chống gian lận hoa hồng)
     * 3. Ghi nhận thuế TNCN 10% trên giao dịch rút tiền
     */
    public function up(): void
    {
        // 1. Bổ sung cột KYC & deferred reward vào bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_card_front', 500)->nullable()->after('bank_account_name')
                ->comment('Đường dẫn ảnh CCCD mặt trước');
            $table->string('id_card_back', 500)->nullable()->after('id_card_front')
                ->comment('Đường dẫn ảnh CCCD mặt sau');
            $table->string('tax_code', 20)->nullable()->after('id_card_back')
                ->comment('Mã số thuế cá nhân (MST)');
            $table->timestamp('kyc_verified_at')->nullable()->after('tax_code')
                ->comment('Thời điểm Admin xác minh KYC (null = chưa xác minh)');
            $table->boolean('pending_referral_reward')->default(false)->after('kyc_verified_at')
                ->comment('True = user mới chưa hoàn thành hồ sơ con, chưa tính hoa hồng cho referrer');
        });

        // 2. Bổ sung cột thuế TNCN vào bảng transactions (cho withdraw)
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('tax_amount', 15, 0)->default(0)->after('amount')
                ->comment('Số tiền thuế TNCN 10% bị khấu trừ');
            $table->decimal('net_amount', 15, 0)->default(0)->after('tax_amount')
                ->comment('Số tiền thực nhận sau thuế (amount - tax_amount)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'id_card_front',
                'id_card_back',
                'tax_code',
                'kyc_verified_at',
                'pending_referral_reward',
            ]);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'net_amount']);
        });
    }
};
