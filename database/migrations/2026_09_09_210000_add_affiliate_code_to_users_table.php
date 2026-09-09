<?php

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
        if (!Schema::hasColumn('users', 'affiliate_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('affiliate_code', 50)
                    ->nullable()
                    ->unique()
                    ->after('code');
            });
        }

        // Tự động gán mã affiliate định dạng CC001, CC002... cho các người dùng chưa có mã theo thứ tự id
        $maxCode = DB::table('users')
            ->where('affiliate_code', 'LIKE', 'CC%')
            ->orderByRaw('CAST(SUBSTRING(affiliate_code, 3) AS UNSIGNED) DESC')
            ->value('affiliate_code');

        $counter = 1;
        if ($maxCode && preg_match('/^CC(\d+)$/', $maxCode, $matches)) {
            $counter = (int)$matches[1] + 1;
        }

        DB::table('users')
            ->whereNull('affiliate_code')
            ->orderBy('id', 'asc')
            ->chunkById(200, function ($users) use (&$counter) {
                foreach ($users as $user) {
                    $code = sprintf('CC%03d', $counter++);
                    DB::table('users')->where('id', $user->id)->update([
                        'affiliate_code' => $code,
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'affiliate_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('affiliate_code');
            });
        }
    }
};
