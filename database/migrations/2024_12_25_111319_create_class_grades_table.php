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
    public function up()
    {
        Schema::create('class_grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->decimal('semester1_grade', 5, 2);
            $table->decimal('semester2_grade', 5, 2);
            $table->decimal('full_year_grade', 5, 2);
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Draft->value);

            $table->timestamps();
            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_grades');
    }
};
