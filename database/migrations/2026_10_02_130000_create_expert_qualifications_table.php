<?php

use App\Enums\DefaultStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expert_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_id')->constrained('experts')->cascadeOnDelete();
            $table->string('degree_name');
            $table->string('institution')->nullable();
            $table->string('graduation_year')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(DefaultStatus::Published->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_qualifications');
    }
};
