<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Thêm cột affiliate_terms_accepted_at vào bảng users.
     * Backfill: Tất cả user đã có referrer_id sẽ được đặt = created_at.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('affiliate_terms_accepted_at')
                ->nullable()
                ->after('pending_referral_reward')
                ->comment('Thời điểm user đồng ý Điều kiện & Điều khoản Affiliate');
        });

        // Backfill: User cũ đã liên kết referrer_id coi như đã đồng ý từ trước
        DB::table('users')
            ->whereNotNull('referrer_id')
            ->whereNull('affiliate_terms_accepted_at')
            ->update([
                'affiliate_terms_accepted_at' => DB::raw('created_at'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('affiliate_terms_accepted_at');
        });
    }
};
