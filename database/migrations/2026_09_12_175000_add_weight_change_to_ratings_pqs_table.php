<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thêm cột weight_change (chênh lệch cân nặng so với chuẩn WHO) vào bảng ratings_pqs.
     * Đây là cột additive - không ảnh hưởng V1 production.
     */
    public function up(): void
    {
        Schema::table('ratings_pqs', function (Blueprint $table) {
            $table->double('weight_change')->nullable()->after('height_change')
                ->comment('Chênh lệch cân nặng so với chuẩn WHO (kg)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings_pqs', function (Blueprint $table) {
            $table->dropColumn('weight_change');
        });
    }
};
