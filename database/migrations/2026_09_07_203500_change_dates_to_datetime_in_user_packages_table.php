<?php

use Illuminate\Database\Migrations\Migration;
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
        DB::statement('ALTER TABLE `user_packages` MODIFY `start_date` DATETIME NOT NULL, MODIFY `end_date` DATETIME NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `user_packages` MODIFY `start_date` TIMESTAMP NOT NULL, MODIFY `end_date` TIMESTAMP NOT NULL');
    }
};
