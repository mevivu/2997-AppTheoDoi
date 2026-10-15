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
        Schema::table('app_features', function (Blueprint $table) {
            $table->boolean('enabled_dev')->default(true)->after('enabled_ios')
                ->comment('Bật/tắt riêng cho môi trường Development / Testing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_features', function (Blueprint $table) {
            $table->dropColumn('enabled_dev');
        });
    }
};
