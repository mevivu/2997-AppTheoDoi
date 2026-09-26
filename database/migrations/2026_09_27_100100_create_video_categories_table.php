<?php

use App\Enums\ActiveStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('video_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('age_group_id')->constrained('age_groups')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('video_categories')->nullOnDelete();
            $table->string('name', 200);
            $table->string('slug', 200);
            $table->string('icon', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_categories');
    }
};
