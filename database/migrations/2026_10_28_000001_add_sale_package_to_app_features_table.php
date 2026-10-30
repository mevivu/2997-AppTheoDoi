<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\AppFeature;

return new class extends Migration {
    public function up(): void
    {
        $exists = DB::table('app_features')->where('code', 'sale_package')->exists();
        if (!$exists) {
            $now = now();
            DB::table('app_features')->insert([
                'code' => 'sale_package',
                'name' => 'Ưu đãi & Quà tặng (Gói Sale)',
                'group' => 'account',
                'enabled_android' => true,
                'enabled_ios' => true,
                'enabled_dev' => true,
                'disabled_mode' => 'hide',
                'disabled_message' => null,
                'sort_order' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            AppFeature::flushCache();
        }
    }

    public function down(): void
    {
        DB::table('app_features')->where('code', 'sale_package')->delete();
        AppFeature::flushCache();
    }
};
