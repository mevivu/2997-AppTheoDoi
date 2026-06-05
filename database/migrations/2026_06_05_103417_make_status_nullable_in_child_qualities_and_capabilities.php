<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE child_qualities MODIFY COLUMN quality_status ENUM('not_achieved', 'achieved', 'good') NULL DEFAULT NULL");
        DB::statement("ALTER TABLE child_capabilities MODIFY COLUMN capability_status ENUM('not_achieved', 'achieved', 'good') NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE child_qualities MODIFY COLUMN quality_status ENUM('not_achieved', 'achieved', 'good') NOT NULL DEFAULT 'not_achieved'");
        DB::statement("ALTER TABLE child_capabilities MODIFY COLUMN capability_status ENUM('not_achieved', 'achieved', 'good') NOT NULL DEFAULT 'not_achieved'");
    }
};
