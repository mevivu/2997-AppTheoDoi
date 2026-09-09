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
        // Kiểm tra xem index đã tồn tại trên cột code chưa trước khi tạo
        $hasIndex = false;
        try {
            $indexes = DB::select("SHOW INDEX FROM users WHERE Column_name = 'code'");
            $hasIndex = !empty($indexes);
        } catch (\Throwable $e) {
            $hasIndex = false;
        }

        if (!$hasIndex) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasIndex = false;
        try {
            $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_code_index'");
            $hasIndex = !empty($indexes);
        } catch (\Throwable $e) {
            $hasIndex = false;
        }

        if ($hasIndex) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['code']);
            });
        }
    }
};
