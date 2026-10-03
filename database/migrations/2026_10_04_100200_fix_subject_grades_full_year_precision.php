<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE subject_grades MODIFY full_year_grade DECIMAL(5,2) NULL');

        Schema::table('subject_grades', function (Blueprint $table) {
            $table->string('full_year_grade_source', 20)->nullable()->after('full_year_grade');
        });
    }

    public function down(): void
    {
        Schema::table('subject_grades', function (Blueprint $table) {
            $table->dropColumn('full_year_grade_source');
        });
    }
};
