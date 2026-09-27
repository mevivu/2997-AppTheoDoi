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
        // 1. Bảng Danh mục bài học (Lesson Categories)
        Schema::create('lesson_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('age_group_id')->constrained('age_groups')->cascadeOnDelete();
            $table->string('pillar', 50)->index()->comment('Trụ cột/Lĩnh vực: pq, iq, eq, aq, thai_giao (Enum EducationPillar)');
            $table->string('key', 50)->index()->comment('Định danh chuyên môn: height, math_logic... (Enum LessonCategoryKey)');
            $table->string('name', 191);
            $table->string('slug', 191)->nullable();
            $table->string('icon', 255)->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);
            $table->timestamps();

            $table->index(['age_group_id', 'pillar']);
            $table->index(['pillar', 'key']);
        });

        // 2. Bảng Bài học (Lessons)
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_category_id')->constrained('lesson_categories')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255)->nullable();
            $table->text('description')->nullable()->comment('Tóm tắt bài học');
            $table->longText('content')->nullable()->comment('Nội dung chi tiết/hướng dẫn phụ huynh');
            $table->string('difficulty', 50)->default('easy')->comment('Độ khó: easy (Dễ), medium (Có trợ giúp), hard (Khó/Tự lập)');
            $table->string('frequency', 100)->nullable()->comment('Tần suất gợi ý: vd 3 lần/tuần, hàng ngày');
            $table->text('benefit')->nullable()->comment('Lợi ích mang lại cho bé');
            $table->text('tools')->nullable()->comment('Dụng cụ/Học cụ cần chuẩn bị');
            $table->string('access_type', 50)->default('free')->comment('Phân quyền: free (Miễn phí), vip (Gói VIP)');
            $table->unsignedInteger('view_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);
            $table->timestamps();

            $table->index(['lesson_category_id', 'status']);
            $table->index(['access_type', 'status']);
        });

        // 3. Bảng Video của bài học (Lesson Videos - hỗ trợ 2 hoặc nhiều video mỗi bài học)
        Schema::create('lesson_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('title', 255)->nullable()->comment('Tiêu đề video, vd: Video 1 - Hướng dẫn, Video 2 - Thực hành');
            $table->string('video_type', 50)->default('youtube')->comment('Nguồn: youtube, r2');
            $table->string('video_url', 500)->nullable()->comment('URL YouTube hoặc CDN R2');
            $table->string('video_path', 500)->nullable()->comment('S3/R2 object key');
            $table->string('thumbnail', 500)->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['lesson_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_videos');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('lesson_categories');
    }
};
