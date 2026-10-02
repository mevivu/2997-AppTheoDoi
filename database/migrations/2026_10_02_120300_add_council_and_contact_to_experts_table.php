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
        Schema::table('experts', function (Blueprint $table) {
            /** 1: Hội đồng Cố vấn Chiến lược, 2: Hội đồng Tư vấn Chuyên môn */
            $table->tinyInteger('council_type')->default(2)->after('id');
            $table->string('workplace')->nullable()->after('hospital');
            $table->string('contact_link')->nullable()->after('bio');
            $table->string('contact_phone')->nullable()->after('contact_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('experts', function (Blueprint $table) {
            $table->dropColumn(['council_type', 'workplace', 'contact_link', 'contact_phone']);
        });
    }
};
