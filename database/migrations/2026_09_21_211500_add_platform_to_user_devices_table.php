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
        if (Schema::hasTable('user_devices') && !Schema::hasColumn('user_devices', 'platform')) {
            Schema::table('user_devices', function (Blueprint $table) {
                $table->string('platform', 30)->nullable()->after('device_name')->index()->comment('Hệ điều hành thiết bị: ios, android, web');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable('user_devices') && Schema::hasColumn('user_devices', 'platform')) {
            Schema::table('user_devices', function (Blueprint $table) {
                $table->dropColumn('platform');
            });
        }
    }
};
