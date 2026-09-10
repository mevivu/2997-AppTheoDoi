<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'referrer_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('referrer_id')
                    ->nullable()
                    ->after('affiliate_code')
                    ->comment('ID người dùng đã giới thiệu');

                $table->foreign('referrer_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');

                $table->index('referrer_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'referrer_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['referrer_id']);
                $table->dropIndex(['referrer_id']);
                $table->dropColumn('referrer_id');
            });
        }
    }
};
