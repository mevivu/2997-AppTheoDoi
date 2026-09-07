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
            if (!Schema::hasColumn('packages', 'discount_type')) {
                $table->string('discount_type', 20)->default('none')->after('type');
            }
            if (!Schema::hasColumn('packages', 'discount_value')) {
                $table->decimal('discount_value', 15, 0)->default(0)->after('discount_type');
            }
            if (!Schema::hasColumn('packages', 'discount_code')) {
                $table->string('discount_code', 50)->nullable()->after('discount_value');
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
            if (Schema::hasColumn('packages', 'discount_code')) {
                $columnsToDrop[] = 'discount_code';
            }
            if (Schema::hasColumn('packages', 'discount_value')) {
                $columnsToDrop[] = 'discount_value';
            }
            if (Schema::hasColumn('packages', 'discount_type')) {
                $columnsToDrop[] = 'discount_type';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
