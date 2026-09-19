<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('memo_themes') && !Schema::hasColumn('memo_themes', 'age')) {
            Schema::table('memo_themes', function (Blueprint $table) {
                $table->integer('age')->default(1)->after('code')->comment('Độ tuổi áp dụng chủ đề (ví dụ: 1, 2, 3...)');
            });

            // Cập nhật tất cả các chủ đề hiện tại về mặc định là 1 tuổi
            DB::table('memo_themes')->update(['age' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable('memo_themes') && Schema::hasColumn('memo_themes', 'age')) {
            Schema::table('memo_themes', function (Blueprint $table) {
                $table->dropColumn('age');
            });
        }
    }
};
