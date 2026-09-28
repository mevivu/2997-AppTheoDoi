<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lesson_difficulty_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('child_id')
                ->constrained('children')
                ->cascadeOnDelete();
            $table->foreignId('lesson_id')
                ->constrained('lessons')
                ->cascadeOnDelete();
            $table->string('difficulty_level', 20)
                ->comment('Mức đánh giá từ phụ huynh: easy, with_help, hard');
            $table->timestamps();

            // Mỗi bé chỉ có 1 đánh giá cho 1 bài học (từ cùng 1 phụ huynh)
            $table->unique(
                ['user_id', 'child_id', 'lesson_id'],
                'uq_user_child_lesson_rating'
            );

            // Index cho query thống kê phần trăm theo bài học
            $table->index(
                ['lesson_id', 'difficulty_level'],
                'idx_lesson_difficulty_stats'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_difficulty_ratings');
    }
};
