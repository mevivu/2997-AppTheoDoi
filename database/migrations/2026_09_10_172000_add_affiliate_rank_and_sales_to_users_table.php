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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'affiliate_rank')) {
                $table->tinyInteger('affiliate_rank')
                    ->default(1)
                    ->comment('Cấp bậc affiliate: 1: Mẹ Đồng, 2: Mẹ Bạc, 3: Mẹ Vàng, 4: Mẹ Kim Cương')
                    ->after('referrer_id');
            }

            if (!Schema::hasColumn('users', 'affiliate_total_sales')) {
                $table->decimal('affiliate_total_sales', 15, 0)
                    ->default(0)
                    ->comment('Tổng doanh số giới thiệu tích lũy (VNĐ)')
                    ->after('affiliate_rank');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'affiliate_total_sales')) {
                $table->dropColumn('affiliate_total_sales');
            }
            if (Schema::hasColumn('users', 'affiliate_rank')) {
                $table->dropColumn('affiliate_rank');
            }
        });
    }
};
