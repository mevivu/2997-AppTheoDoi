<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'game_plays')) {
                $table->dropColumn('game_plays');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'game_plays')) {
                $table->unsignedInteger('game_plays')
                    ->default(3)
                    ->after('type')
                    ->comment('Số lần chơi Memo Game (1 lần = 1 điểm, mặc định 3)');
            }
        });
    }
};
