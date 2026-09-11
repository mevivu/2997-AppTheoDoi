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
        // 1. Dọn dẹp bảng withdraw_requests cũ vì đã chuyển sang dùng trực tiếp bảng transactions
        Schema::dropIfExists('withdraw_requests');

        // 2. Chuyển package_id thành nullable và type thành VARCHAR(50)
        DB::statement("ALTER TABLE `transactions` MODIFY `package_id` BIGINT UNSIGNED NULL;");
        DB::statement("ALTER TABLE `transactions` MODIFY `type` VARCHAR(50) NOT NULL DEFAULT 'payment';");

        // 3. Bổ sung các thông tin phục vụ lệnh rút tiền hoa hồng
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('bank_name', 100)->nullable()->after('amount')->comment('Tên ngân hàng nhận tiền');
            $table->string('bank_account_number', 50)->nullable()->after('bank_name')->comment('Số tài khoản ngân hàng');
            $table->string('bank_account_name', 150)->nullable()->after('bank_account_number')->comment('Tên chủ tài khoản');
            $table->date('scheduled_payout_date')->nullable()->after('status')->comment('Ngày Thứ 5 dự kiến chi trả');
            $table->text('admin_note')->nullable()->after('scheduled_payout_date')->comment('Ghi chú của admin, mã biên lai hoặc lý do từ chối');
            $table->unsignedBigInteger('processed_by')->nullable()->after('admin_note')->comment('Admin duyệt / từ chối lệnh');
            $table->timestamp('processed_at')->nullable()->after('processed_by')->comment('Thời điểm admin xử lý');

            $table->foreign('processed_by')->references('id')->on('admins')->onDelete('set null');
            $table->index('scheduled_payout_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->dropColumn([
                'bank_name',
                'bank_account_number',
                'bank_account_name',
                'scheduled_payout_date',
                'admin_note',
                'processed_by',
                'processed_at',
            ]);
        });
    }
};
