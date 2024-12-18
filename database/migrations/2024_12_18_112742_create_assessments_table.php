<?php

use App\Enums\Assessment\AssessmentType;
use App\Enums\OpenStatus;
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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->enum('type', AssessmentType::getValues())->default(AssessmentType::AQ->value);
            $table->enum('checked', OpenStatus::getValues())->default(OpenStatus::OFF->value);
            $table->timestamps();

            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
