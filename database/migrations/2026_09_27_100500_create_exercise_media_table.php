<?php

use App\Enums\Exercise\ExerciseMediaType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exercise_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained('exercises')->cascadeOnDelete();
            $table->enum('media_type', ExerciseMediaType::getValues())->default(ExerciseMediaType::IMAGE->value);
            $table->string('media_file', 1000);
            $table->string('thumbnail', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_media');
    }
};
