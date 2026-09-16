<?php

use App\Enums\ActiveStatus;
use App\Enums\Group\GroupType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // 1. Alter question_groups.type to VARCHAR(50) to support new IQ groups
        try {
            DB::statement("ALTER TABLE question_groups MODIFY COLUMN type VARCHAR(50) DEFAULT 'empathy'");
        } catch (\Throwable $e) {
            // Ignore if already varchar
        }

        // 2. Add IQ group breakdown columns to ratings table
        Schema::table('ratings', function (Blueprint $table) {
            if (!Schema::hasColumn('ratings', 'linguistic')) {
                $table->string('linguistic', 191)->nullable()->after('optimism')->comment('Điểm nhóm Ngôn ngữ');
            }
            if (!Schema::hasColumn('ratings', 'logic_math')) {
                $table->string('logic_math', 191)->nullable()->after('linguistic')->comment('Điểm nhóm Toán học & Logic');
            }
            if (!Schema::hasColumn('ratings', 'visual')) {
                $table->string('visual', 191)->nullable()->after('logic_math')->comment('Điểm nhóm Hình ảnh');
            }
            if (!Schema::hasColumn('ratings', 'memory')) {
                $table->string('memory', 191)->nullable()->after('visual')->comment('Điểm nhóm Trí nhớ');
            }
        });

        // 3. Seed 4 IQ question groups if not already present
        $iqGroups = [
            [
                'name' => 'Ngôn ngữ (Linguistic)',
                'description' => 'Đánh giá khả năng ngôn ngữ, hiểu từ vựng, diễn đạt và đọc hiểu của trẻ.',
                'type' => GroupType::Linguistic->value,
                'status' => ActiveStatus::Active->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toán học & Logic (Logic & Math)',
                'description' => 'Đánh giá khả năng tính toán, tư duy quy luật, giải quyết vấn đề logic.',
                'type' => GroupType::LogicMath->value,
                'status' => ActiveStatus::Active->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hình ảnh (Visual & Spatial)',
                'description' => 'Đánh giá khả năng nhận thức không gian, hình học, đối xứng và tư duy trực quan.',
                'type' => GroupType::Visual->value,
                'status' => ActiveStatus::Active->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Trí nhớ (Memory)',
                'description' => 'Đánh giá khả năng ghi nhớ ngắn hạn, tập trung và xử lý thông tin.',
                'type' => GroupType::Memory->value,
                'status' => ActiveStatus::Active->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($iqGroups as $group) {
            $exists = DB::table('question_groups')->where('type', $group['type'])->exists();
            if (!$exists) {
                DB::table('question_groups')->insert($group);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $columns = ['linguistic', 'logic_math', 'visual', 'memory'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('ratings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        DB::table('question_groups')->whereIn('type', [
            GroupType::Linguistic->value,
            GroupType::LogicMath->value,
            GroupType::Visual->value,
            GroupType::Memory->value,
        ])->delete();
    }
};
