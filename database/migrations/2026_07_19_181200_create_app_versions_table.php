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

        // Insert permissions and module
        // Module ID: 33
        // Permissions ID: 125, 126
        DB::table('modules')->insert([
            'id' => 33,
            'name' => 'Quản lý Phiên bản',
            'description' => '<p>Quản lý Phiên bản Ứng dụng</p>',
            'status' => 2,
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
        ]);

        DB::table('permissions')->insert([
            [
                'id' => 125,
                'title' => 'Xem Phiên bản',
                'name' => 'viewAppVersion',
                'guard_name' => 'admin',
                'module_id' => 33,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'id' => 126,
                'title' => 'Sửa Phiên bản',
                'name' => 'updateAppVersion',
                'guard_name' => 'admin',
                'module_id' => 33,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
        ]);

        DB::table('role_has_permissions')->insert([
            [
                'permission_id' => 125,
                'role_id' => 1,
            ],
            [
                'permission_id' => 126,
                'role_id' => 1,
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
        DB::table('role_has_permissions')->whereIn('permission_id', [125, 126])->delete();
        DB::table('permissions')->whereIn('id', [125, 126])->delete();
        DB::table('modules')->where('id', 33)->delete();
        Schema::dropIfExists('app_versions');
    }
};
