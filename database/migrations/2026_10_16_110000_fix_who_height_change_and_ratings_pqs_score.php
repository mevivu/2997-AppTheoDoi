<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Sửa kiểu dữ liệu cột score từ INT UNSIGNED sang DECIMAL(4,1) để lưu điểm có phần thập phân (vd: 9.3)
        if (Schema::hasTable('ratings_pqs') && Schema::hasColumn('ratings_pqs', 'score')) {
            DB::statement("ALTER TABLE ratings_pqs MODIFY COLUMN score DECIMAL(4,1) NULL");
        }

        // 2. Cập nhật dữ liệu chuẩn WHO tại các mốc chuyển tiếp phương pháp đo / dataset
        // - Mốc tháng 24: Chuyển từ đo nằm sang đo đứng (độ lệch kỹ thuật ~0.7cm làm delta sụt xuống 0.18-0.20cm)
        //   Mức tăng trưởng thực tế bình thường là ~0.88 - 0.90 cm/tháng
        // - Mốc tháng 61: Chuyển tiếp giữa dataset WHO 0-5 tuổi và WHO 5-19 tuổi
        if (Schema::hasTable('weight_height_who')) {
            DB::table('weight_height_who')->where('gender', 2)->where('month', 24)->update(['height_change' => 0.90]);
            DB::table('weight_height_who')->where('gender', 1)->where('month', 24)->update(['height_change' => 0.88]);
            DB::table('weight_height_who')->where('gender', 2)->where('month', 61)->update(['height_change' => 0.47]);
            DB::table('weight_height_who')->where('gender', 1)->where('month', 61)->update(['height_change' => 0.45]);
        }

        // 3. Tính lại điểm PQ cho các bản ghi bị ảnh hưởng bởi mốc 24 tháng gần đây (bao gồm child 19136)
        try {
            $pqService = app(\App\Api\V1\Services\RatingPQ\RatingPQService::class);
            $affectedRecords = \App\Models\RatingPQ::whereIn('age_month', [24, 61])
                ->orWhere('child_id', 19136)
                ->get();

            foreach ($affectedRecords as $record) {
                if (!$record->child) continue;
                $child = $record->child;
                $gender = $child->gender instanceof \App\Enums\User\Gender ? $child->gender->value : $child->gender;
                $assessmentDate = \Illuminate\Support\Carbon::parse($record->assessment_date);
                $birthday = \Illuminate\Support\Carbon::parse($child->birthday);
                $monthCalculate = round($birthday->diffInDays($assessmentDate) / 30.5);
                $ageCalculate = round($birthday->diffInDays($assessmentDate) / 365.3);

                $newScore = $pqService->calculateScore(
                    $record->bmi,
                    $ageCalculate,
                    $gender,
                    $child->id,
                    $record->endurance,
                    $record->strength,
                    $record->height,
                    $assessmentDate,
                    $record->weight,
                    $monthCalculate
                );

                if ($newScore !== null) {
                    $record->score = $newScore;
                    $record->saveQuietly();
                }
            }
        } catch (\Throwable $e) {
            // Không chặn migration nếu có lỗi logic phụ
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('ratings_pqs') && Schema::hasColumn('ratings_pqs', 'score')) {
            DB::statement("ALTER TABLE ratings_pqs MODIFY COLUMN score INT UNSIGNED NULL");
        }

        if (Schema::hasTable('weight_height_who')) {
            DB::table('weight_height_who')->where('gender', 2)->where('month', 24)->update(['height_change' => 0.20]);
            DB::table('weight_height_who')->where('gender', 1)->where('month', 24)->update(['height_change' => 0.18]);
            DB::table('weight_height_who')->where('gender', 2)->where('month', 61)->update(['height_change' => 0.18]);
            DB::table('weight_height_who')->where('gender', 1)->where('month', 61)->update(['height_change' => 0.34]);
        }
    }
};
