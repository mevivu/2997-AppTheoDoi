<?php

use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);
            $table->enum('exercise_type', ExerciseType::getValues())->default(ExerciseType::PHYSICAL->value);
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
        Schema::dropIfExists('exercises');
    }
};
