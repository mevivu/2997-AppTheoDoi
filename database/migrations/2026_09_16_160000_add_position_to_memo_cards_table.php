<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('memo_cards')) {
            if (!Schema::hasColumn('memo_cards', 'position')) {
                Schema::table('memo_cards', function (Blueprint $table) {
                    $table->integer('position')->default(0)->after('status')->index()->comment('Thứ tự sắp xếp');
                });
            }

            // Khởi tạo thứ tự ban đầu cho các thẻ bài hiện có theo từng chủ đề
            $themes = DB::table('memo_cards')->distinct()->pluck('memo_theme_id');
            foreach ($themes as $themeId) {
                $cards = DB::table('memo_cards')
                    ->where('memo_theme_id', $themeId)
                    ->orderBy('id', 'asc')
                    ->get(['id']);

                foreach ($cards as $idx => $card) {
                    DB::table('memo_cards')
                        ->where('id', $card->id)
                        ->update(['position' => $idx + 1]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('memo_cards')) {
            if (Schema::hasColumn('memo_cards', 'position')) {
                Schema::table('memo_cards', function (Blueprint $table) {
                    $table->dropColumn('position');
                });
            }
        }
    }
};
