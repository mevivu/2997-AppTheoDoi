<?php

use App\Enums\ActiveStatus;
use App\Enums\Video\VideoAccessType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_category_id')->constrained('video_categories')->cascadeOnDelete();
            $table->string('title', 300);
            $table->text('description')->nullable();
            $table->string('video_url', 1000);
            $table->string('thumbnail', 500)->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->enum('access_type', VideoAccessType::getValues())->default(VideoAccessType::FREE->value);
            $table->boolean('is_preview')->default(false);
            $table->integer('sort_order')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
