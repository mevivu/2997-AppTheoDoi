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
        Schema::table('videos', function (Blueprint $table) {
            if (!Schema::hasColumn('videos', 'video_type')) {
                $table->string('video_type', 20)->default('youtube')->after('video_category_id');
            }
            if (!Schema::hasColumn('videos', 'video_path')) {
                $table->string('video_path', 500)->nullable()->after('video_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (Schema::hasColumn('videos', 'video_path')) {
                $table->dropColumn('video_path');
            }
            if (Schema::hasColumn('videos', 'video_type')) {
                $table->dropColumn('video_type');
            }
        });
    }
};
