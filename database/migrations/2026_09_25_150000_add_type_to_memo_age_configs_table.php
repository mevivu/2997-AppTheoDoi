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
        Schema::table('memo_age_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('memo_age_configs', 'type')) {
                $table->string('type', 30)->default('iq_test')->after('name')->comment('Loại cấu hình: iq_test (Bài test IQ), competition (Giải đấu)');
            }
        });

        // Cập nhật cấu hình dành cho giải đấu (ID 5 hoặc tên chứa Giải đấu) sang loại competition
        DB::table('memo_age_configs')
            ->where('id', 5)
            ->orWhere('name', 'like', '%Giải đấu%')
            ->update(['type' => 'competition']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('memo_age_configs', function (Blueprint $table) {
            if (Schema::hasColumn('memo_age_configs', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
