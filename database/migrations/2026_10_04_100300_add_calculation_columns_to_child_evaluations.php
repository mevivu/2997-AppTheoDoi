<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('child_evaluations', function (Blueprint $table) {
            $table->string('calculated_academic_performance', 32)->nullable()->after('academic_performance');
            $table->string('calculation_status', 20)->nullable()->after('calculated_academic_performance');
            $table->boolean('is_performance_overridden')->default(false)->after('calculation_status');
            $table->text('teacher_remark')->nullable()->after('is_performance_overridden');
            $table->json('calculation_snapshot')->nullable()->after('teacher_remark');
            $table->unsignedSmallInteger('calculation_version')->nullable()->after('calculation_snapshot');
            $table->timestamp('calculated_at')->nullable()->after('calculation_version');
        });
    }

    public function down(): void
    {
        Schema::table('child_evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'calculated_academic_performance',
                'calculation_status',
                'is_performance_overridden',
                'teacher_remark',
                'calculation_snapshot',
                'calculation_version',
                'calculated_at',
            ]);
        });
    }
};
