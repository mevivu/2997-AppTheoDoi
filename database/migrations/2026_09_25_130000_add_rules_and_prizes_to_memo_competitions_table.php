<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('memo_competitions', function (Blueprint $table) {
            if (!Schema::hasColumn('memo_competitions', 'rules')) {
                $table->text('rules')->nullable()->after('description')->comment('Thể lệ chi tiết giải đấu');
            }
            if (!Schema::hasColumn('memo_competitions', 'prizes')) {
                $table->text('prizes')->nullable()->after('rules')->comment('Cơ cấu giải thưởng giải đấu');
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
        Schema::table('memo_competitions', function (Blueprint $table) {
            if (Schema::hasColumn('memo_competitions', 'prizes')) {
                $table->dropColumn('prizes');
            }
            if (Schema::hasColumn('memo_competitions', 'rules')) {
                $table->dropColumn('rules');
            }
        });
    }
};
