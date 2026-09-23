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
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'sale_start_at')) {
                $table->dateTime('sale_start_at')->nullable()->after('is_sale');
            }
            if (!Schema::hasColumn('packages', 'sale_end_at')) {
                $table->dateTime('sale_end_at')->nullable()->after('sale_start_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('packages', 'sale_end_at')) {
                $columnsToDrop[] = 'sale_end_at';
            }
            if (Schema::hasColumn('packages', 'sale_start_at')) {
                $columnsToDrop[] = 'sale_start_at';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
