<?php

use App\Enums\ActiveStatus;
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
        Schema::create('fetal_growth_standards', function (Blueprint $table) {
            $table->id();
            $table->integer('week')->unique()->comment('Tuần tuổi thai (8 - 42)');
            $table->decimal('length', 6, 2)->comment('Chiều dài chuẩn (cm)');
            $table->decimal('weight', 8, 2)->comment('Cân nặng chuẩn (g)');
            $table->decimal('head_circumference', 6, 2)->nullable()->comment('Chu vi đầu chuẩn (cm)');
            $table->text('description')->nullable()->comment('Mô tả phát triển tuần thai');
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('fetal_growth_standards');
    }
};
