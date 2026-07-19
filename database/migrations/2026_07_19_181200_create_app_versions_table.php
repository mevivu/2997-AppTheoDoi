<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('app_type');
            $table->string('platform');
            $table->string('notify');
            $table->string('required');
            $table->string('checking_version')->nullable();
            $table->string('update_url');
            $table->text('release_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert initial data
        DB::table('app_versions')->insert([
            [
                'app_type' => 'user',
                'platform' => 'android',
                'notify' => '1.0.0',
                'required' => '1.0.0',
                'checking_version' => null,
                'update_url' => 'https://play.google.com/store',
                'release_notes' => json_encode(['vi' => 'Phiên bản đầu tiên', 'en' => 'First version']),
                'is_active' => true,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'app_type' => 'user',
                'platform' => 'ios',
                'notify' => '1.0.0',
                'required' => '1.0.0',
                'checking_version' => null,
                'update_url' => 'https://apps.apple.com',
                'release_notes' => json_encode(['vi' => 'Phiên bản đầu tiên', 'en' => 'First version']),
                'is_active' => true,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_versions');
    }
};
