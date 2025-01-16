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
    public function up()
    {
        Schema::create('ratings_pqs', function (Blueprint $table) {
            $table->id();
            $table->date('assessment_date'); // Ngày đánh giá
            $table->unsignedInteger('height'); // Chiều cao, đơn vị cm
            $table->unsignedInteger('weight'); // Cân nặng, đơn vị kg
            $table->unsignedInteger('strength'); // Điểm sức mạnh
            $table->unsignedInteger('endurance'); // Điểm sức bền
            $table->decimal('bmi', 5, 2)->nullable();
            $table->string('bmi_result')->nullable();
            $table->timestamps();
            $table->foreignId('child_id')->nullable()->constrained('children')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings_pqs');
    }
};
