<?php

use App\Enums\Post\PostType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'type')) {
                $table->tinyInteger('type')
                    ->default(PostType::Post->value)
                    ->after('id')
                    ->index()
                    ->comment('1: Bài viết thường, 2: Kiến thức chăm con');
            }

            if (!Schema::hasColumn('posts', 'post_type')) {
                $table->tinyInteger('post_type')
                    ->default(PostType::Post->value)
                    ->after('type')
                    ->index()
                    ->comment('1: Bài viết thường, 2: Kiến thức chăm con');
            }
        });

        // Đồng bộ dữ liệu giữa type và post_type
        if (Schema::hasColumn('posts', 'post_type') && Schema::hasColumn('posts', 'type')) {
            DB::table('posts')->whereNotNull('post_type')->where(function($q) {
                $q->whereNull('type')->orWhere('type', 0);
            })->update([
                'type' => DB::raw('post_type')
            ]);

            DB::table('posts')->whereNotNull('type')->where(function($q) {
                $q->whereNull('post_type')->orWhere('post_type', 0);
            })->update([
                'post_type' => DB::raw('type')
            ]);
        }

        DB::table('posts')->whereNull('type')->orWhere('type', 0)->update([
            'type' => PostType::Post->value,
            'post_type' => PostType::Post->value,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
