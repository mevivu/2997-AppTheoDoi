<?php

use App\Enums\ActiveStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();
        $groups = [
            ['name' => 'Thai giáo', 'min_months' => null, 'max_months' => null, 'sort_order' => 0, 'status' => ActiveStatus::Active->value, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '0-2 tuổi', 'min_months' => 0, 'max_months' => 24, 'sort_order' => 1, 'status' => ActiveStatus::Active->value, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '2-4 tuổi', 'min_months' => 24, 'max_months' => 48, 'sort_order' => 2, 'status' => ActiveStatus::Active->value, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '4-6 tuổi', 'min_months' => 48, 'max_months' => 72, 'sort_order' => 3, 'status' => ActiveStatus::Active->value, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '6-8 tuổi', 'min_months' => 72, 'max_months' => 96, 'sort_order' => 4, 'status' => ActiveStatus::Active->value, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '8-10 tuổi', 'min_months' => 96, 'max_months' => 120, 'sort_order' => 5, 'status' => ActiveStatus::Active->value, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '10-12 tuổi', 'min_months' => 120, 'max_months' => 144, 'sort_order' => 6, 'status' => ActiveStatus::Active->value, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '12-14 tuổi', 'min_months' => 144, 'max_months' => 168, 'sort_order' => 7, 'status' => ActiveStatus::Active->value, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '14+ tuổi', 'min_months' => 168, 'max_months' => null, 'sort_order' => 8, 'status' => ActiveStatus::Active->value, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('age_groups')->insert($groups);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('age_groups')->whereIn('name', [
            'Thai giáo',
            '0-2 tuổi',
            '2-4 tuổi',
            '4-6 tuổi',
            '6-8 tuổi',
            '8-10 tuổi',
            '10-12 tuổi',
            '12-14 tuổi',
            '14+ tuổi',
        ])->delete();
    }
};
