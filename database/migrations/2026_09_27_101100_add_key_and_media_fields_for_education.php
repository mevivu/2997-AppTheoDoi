<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Thêm key vào bảng exercise_categories
        if (Schema::hasTable('exercise_categories')) {
            Schema::table('exercise_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('exercise_categories', 'key')) {
                    $table->string('key', 50)->default('other')->after('topic')->comment('Key định danh chuẩn hóa theo Enum ExerciseSubCategoryKey');
                }
            });
        }

        // 2. Thêm title và video_type vào bảng exercise_media
        if (Schema::hasTable('exercise_media')) {
            Schema::table('exercise_media', function (Blueprint $table) {
                if (!Schema::hasColumn('exercise_media', 'title')) {
                    $table->string('title', 255)->nullable()->after('exercise_id')->comment('Tiêu đề video con (VD: Video 1: Bé xem, Video 2: Phụ huynh hướng dẫn)');
                }
                if (!Schema::hasColumn('exercise_media', 'video_type')) {
                    $table->string('video_type', 20)->default('youtube')->after('media_type')->comment('Nguồn video: youtube, r2');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('exercise_categories')) {
            Schema::table('exercise_categories', function (Blueprint $table) {
                if (Schema::hasColumn('exercise_categories', 'key')) {
                    $table->dropColumn('key');
                }
            });
        }

        if (Schema::hasTable('exercise_media')) {
            Schema::table('exercise_media', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('exercise_media', 'title')) {
                    $cols[] = 'title';
                }
                if (Schema::hasColumn('exercise_media', 'video_type')) {
                    $cols[] = 'video_type';
                }
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
