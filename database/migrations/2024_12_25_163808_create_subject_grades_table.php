<?php

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
        Schema::create('subject_grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_grade_id');
            $table->unsignedBigInteger('subject_id');
            $table->decimal('grade', 5, 2);
            $table->enum('semester', SemesterStatus::getValues())->default(SemesterStatus::Semester1->value);

            $table->timestamps();

            $table->foreign('class_grade_id')->references('id')->on('class_grades')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_grades');
    }
};
