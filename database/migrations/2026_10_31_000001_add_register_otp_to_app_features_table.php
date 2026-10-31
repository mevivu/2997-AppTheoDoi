<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\AppFeature;

return new class extends Migration {
    public function up(): void
    {
        $exists = DB::table('app_features')->where('code', 'register_otp')->exists();
        if (!$exists) {
            $now = now();
            DB::table('app_features')->insert([
                'code' => 'register_otp',
                'name' => 'Xác thực OTP khi đăng ký tài khoản',
                'group' => 'account',
                'enabled_android' => true,
                'enabled_ios' => true,
                'enabled_dev' => true,
                'disabled_mode' => 'hide',
                'disabled_message' => null,
                'sort_order' => 25,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            AppFeature::flushCache();
        }
    }

    public function down(): void
    {
        DB::table('app_features')->where('code', 'register_otp')->delete();
        AppFeature::flushCache();
    }
};
