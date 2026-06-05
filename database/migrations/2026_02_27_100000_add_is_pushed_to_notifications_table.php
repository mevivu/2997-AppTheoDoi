<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('notifications', 'is_pushed')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->boolean('is_pushed')->default(false)->after('status');
                $table->index(['is_pushed', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notifications', 'is_pushed')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex(['is_pushed', 'user_id']);
                $table->dropColumn('is_pushed');
            });
        }
    }
};
