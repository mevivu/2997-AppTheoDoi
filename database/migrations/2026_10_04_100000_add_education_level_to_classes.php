<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('education_level', 20)->nullable()->after('level_group');
        });

        DB::table('classes')->where('id', '<=', 5)->update(['education_level' => 'primary']);
        DB::table('classes')->whereBetween('id', [6, 9])->update(['education_level' => 'lower_secondary']);
        DB::table('classes')->where('id', '>=', 10)->update(['education_level' => 'upper_secondary']);
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('education_level');
        });
    }
};
