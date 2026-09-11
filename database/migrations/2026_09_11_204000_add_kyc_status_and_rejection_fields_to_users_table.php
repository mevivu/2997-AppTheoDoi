<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status', 30)->default('not_submitted')->after('tax_code')
                ->comment('Trạng thái xác minh CCCD: not_submitted, pending, approved, rejected');
            $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_status')
                ->comment('Thời điểm đối tác gửi yêu cầu xác minh CCCD');
            $table->timestamp('kyc_rejected_at')->nullable()->after('kyc_verified_at')
                ->comment('Thời điểm Admin từ chối duyệt CCCD');
            $table->string('kyc_rejection_reason', 500)->nullable()->after('kyc_rejected_at')
                ->comment('Lý do Admin từ chối duyệt CCCD');

            $table->index('kyc_status');
        });

        // Đồng bộ dữ liệu cũ: Nếu user đã có kyc_verified_at thì chuyển sang approved, nếu đã có CCCD mà chưa duyệt thì pending
        DB::table('users')
            ->whereNotNull('kyc_verified_at')
            ->update([
                'kyc_status' => 'approved',
                'kyc_submitted_at' => DB::raw('kyc_verified_at'),
            ]);

        DB::table('users')
            ->whereNull('kyc_verified_at')
            ->whereNotNull('id_card_front')
            ->whereNotNull('id_card_back')
            ->whereNotNull('tax_code')
            ->update([
                'kyc_status' => 'pending',
                'kyc_submitted_at' => DB::raw('updated_at'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['kyc_status']);
            $table->dropColumn([
                'kyc_status',
                'kyc_submitted_at',
                'kyc_rejected_at',
                'kyc_rejection_reason',
            ]);
        });
    }
};
