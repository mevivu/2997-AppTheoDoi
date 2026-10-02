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
        // 1. Bảng Chuyên gia / Bác sĩ cố vấn
        Schema::create('experts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('hospital')->nullable();
            $table->text('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(DefaultStatus::Published->value);
            $table->timestamps();
        });

        // 2. Bảng Danh mục Chuyên đề Góc Chuyên gia
        Schema::create('expert_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(DefaultStatus::Published->value);
            $table->timestamps();
        });

        // 3. Bảng Bài viết & Lời khuyên Chuyên môn
        Schema::create('expert_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('expert_id')->nullable()->constrained('experts')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('expert_categories')->nullOnDelete();
            $table->foreignId('age_group_id')->nullable()->constrained('age_groups')->nullOnDelete();
            $table->text('image')->nullable();
            $table->string('reading_time')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('expert_quote')->nullable();
            $table->longText('content')->nullable();
            $table->tinyInteger('is_featured')->default(0);
            $table->integer('views')->default(0);
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(DefaultStatus::Published->value);
            $table->dateTime('posted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_posts');
        Schema::dropIfExists('expert_categories');
        Schema::dropIfExists('experts');
    }
};
