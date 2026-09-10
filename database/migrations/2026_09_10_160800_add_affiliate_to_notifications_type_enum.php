<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `type` ENUM('unclassified', 'payment', 'lock', 'login_another_device', 'affiliate') NOT NULL DEFAULT 'unclassified'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `type` ENUM('unclassified', 'payment', 'lock', 'login_another_device') NOT NULL DEFAULT 'unclassified'");
    }
};
