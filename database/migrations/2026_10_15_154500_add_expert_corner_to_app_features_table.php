<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\AppFeature;

return new class extends Migration {
    public function up(): void
    {
        $exists = DB::table('app_features')->where('code', 'expert_corner')->exists();
        if (!$exists) {
            $now = now();
            DB::table('app_features')->insert([
                'code' => 'expert_corner',
                'name' => 'Góc chuyên gia',
                'group' => 'account',
                'enabled_android' => true,
                'enabled_ios' => true,
                'enabled_dev' => true,
                'disabled_mode' => 'hide',
                'disabled_message' => null,
                'sort_order' => 18,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            AppFeature::flushCache();
        }
    }

    public function down(): void
    {
        DB::table('app_features')->where('code', 'expert_corner')->delete();
        AppFeature::flushCache();
    }
};
