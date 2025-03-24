<?php

use App\Enums\Question\QuestionType;
use App\Enums\VerifiedStatus;
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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->string('score');
            $table->text('description');
            $table->string('tag', 255)->nullable();
            $table->integer('age')->nullable();
            $table->string('result')->nullable();
            $table->string('self_regulation')->nullable();
            $table->string('social_awareness')->nullable();
            $table->string('relationship_management')->nullable();
            $table->string('decision_making')->nullable();
            $table->string('optimism')->nullable();
            $table->integer('endurance')->nullable(); // Khả năng chịu đựng
            $table->integer('flexibility')->nullable(); // Tính linh hoạt
            $table->integer('perseverance')->nullable(); // Tính kiên trì
            $table->integer('positivity')->nullable(); // Tính tích cực
            $table->integer('self_reflection')->nullable(); // Khả năng tự phản hồi
            $table->string('badge_image')->nullable();
            $table->string('label', 255)->nullable();
            $table->enum('type', QuestionType::getValues())->default(QuestionType::EQ->value);
            $table->enum('status', VerifiedStatus::getValues())->default(VerifiedStatus::Pending->value);
            $table->foreignId('child_id')->nullable()->constrained('children')->onDelete('cascade');
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
        Schema::dropIfExists('ratings');
    }
};
