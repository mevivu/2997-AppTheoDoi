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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('parent_rank')->default(0)->after('affiliate_total_sales')->comment('Cấp bậc phụ huynh hiện tại (ParentRank)');
            $table->decimal('parent_rank_points', 5, 2)->default(0)->after('parent_rank')->comment('Điểm phân hạng phụ huynh hiện tại');
            $table->string('parent_rank_period', 7)->nullable()->after('parent_rank_points')->comment('Kỳ đánh giá gần nhất (YYYY-MM)');
            $table->timestamp('parent_rank_updated_at')->nullable()->after('parent_rank_period')->comment('Thời điểm cập nhật phân hạng phụ huynh');

            $table->index(['parent_rank', 'parent_rank_points']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['parent_rank', 'parent_rank_points']);
            $table->dropColumn([
                'parent_rank',
                'parent_rank_points',
                'parent_rank_period',
                'parent_rank_updated_at',
            ]);
        });
    }
};
