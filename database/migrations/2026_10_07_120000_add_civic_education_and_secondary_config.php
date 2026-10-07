<?php

use App\Enums\ActiveStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $subject = DB::table('subjects')->where('name', 'Giáo dục công dân')->first();
        if (!$subject) {
            $subjectId = DB::table('subjects')->insertGetId([
                'name' => 'Giáo dục công dân',
                'status' => ActiveStatus::Active->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $subjectId = $subject->id;
        }

        foreach ([6, 7, 8, 9] as $classId) {
            DB::table('class_subject')->updateOrInsert(
                [
                    'class_id' => $classId,
                    'subject_id' => $subjectId,
                ],
                [
                    'evaluation_method' => 'score',
                    'is_required' => true,
                    'sort_order' => 4,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $subject = DB::table('subjects')->where('name', 'Giáo dục công dân')->first();
        if ($subject) {
            DB::table('class_subject')->where('subject_id', $subject->id)->delete();
        }
    }
};
