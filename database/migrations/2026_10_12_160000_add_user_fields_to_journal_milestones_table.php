<?php

use App\Enums\Permission\PermissionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('journal_milestones', function (Blueprint $table) {
            $table->string('type', 20)->default(PermissionType::ADMIN->value)->after('stage');
            $table->unsignedBigInteger('child_id')->nullable()->after('type');
            $table->unsignedBigInteger('user_id')->nullable()->after('child_id');

            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['type', 'child_id', 'stage', 'position'], 'idx_milestones_type_child_stage');
        });
    }

    public function down(): void
    {
        Schema::table('journal_milestones', function (Blueprint $table) {
            $table->dropForeign(['child_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex('idx_milestones_type_child_stage');
            $table->dropColumn(['type', 'child_id', 'user_id']);
        });
    }
};
