<?php

use App\Enums\ActiveStatus;
use App\Enums\ChildEvaluation\AcademicRating;
use App\Enums\ChildEvaluation\ConductRating;
use App\Enums\Semester\SemesterStatus;
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
        Schema::create('child_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_grade_id');
            $table->decimal('average_score', 4, 2)->default(0);
            $table->enum('conduct', ConductRating::getValues())->default(ConductRating::Good->value);
            $table->enum('academic_performance', AcademicRating::getValues())->default(AcademicRating::Good->value);
            $table->enum('semester', SemesterStatus::getValues())->default(SemesterStatus::Semester1->value);
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Draft->value);

            $table->foreign('class_grade_id')->references('id')->on('class_grades')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('child_evaluations');
    }
};
