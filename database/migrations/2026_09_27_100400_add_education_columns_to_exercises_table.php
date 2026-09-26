<?php

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
        Schema::table('exercises', function (Blueprint $table) {
            $table->foreignId('exercise_category_id')->nullable()->after('id')->constrained('exercise_categories')->nullOnDelete();
            $table->text('content')->nullable()->after('description');
            $table->string('difficulty', 20)->nullable()->after('content');
            $table->string('frequency', 100)->nullable()->after('difficulty');
            $table->text('benefit')->nullable()->after('frequency');
            $table->text('tools')->nullable()->after('benefit');
            $table->enum('access_type', VideoAccessType::getValues())->default(VideoAccessType::FREE->value)->after('tools');
            $table->unsignedInteger('practice_count')->default(0)->after('access_type');
            $table->integer('sort_order')->default(0)->after('practice_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropForeign(['exercise_category_id']);
            $table->dropColumn([
                'exercise_category_id',
                'content',
                'difficulty',
                'frequency',
                'benefit',
                'tools',
                'access_type',
                'practice_count',
                'sort_order',
            ]);
        });
    }
};
