<?php

use App\Enums\Journal\MilestoneStage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journal_milestones', function (Blueprint $table) {
            $table->id();
            $table->string('stage', 30)->index();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('age_from_month')->default(0);
            $table->unsignedSmallInteger('age_to_month')->default(12);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_milestones');
    }
};
