<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSubjectEvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [];

        // Classes 1-2 (ids: 1, 2)
        foreach ([1, 2] as $classId) {
            $configs[$classId] = [
                // Level with score
                ['subject_id' => 2, 'method' => 'level_with_score', 'required' => true, 'order' => 1], // Toán
                ['subject_id' => 12, 'method' => 'level_with_score', 'required' => true, 'order' => 2], // Tiếng Việt
                // Level only
                ['subject_id' => 4, 'method' => 'level', 'required' => true, 'order' => 3], // Đạo đức
                ['subject_id' => 5, 'method' => 'level', 'required' => true, 'order' => 4], // Tự nhiên và Xã hội
                ['subject_id' => 9, 'method' => 'level', 'required' => true, 'order' => 5], // Giáo dục thể chất
                ['subject_id' => 26, 'method' => 'level', 'required' => true, 'order' => 6], // Âm nhạc
                ['subject_id' => 27, 'method' => 'level', 'required' => true, 'order' => 7], // Mĩ thuật
                ['subject_id' => 11, 'method' => 'level', 'required' => true, 'order' => 8], // Hoạt động trải nghiệm
            ];
        }

        // Class 3 (id: 3)
        $configs[3] = [
            // Level with score
            ['subject_id' => 2, 'method' => 'level_with_score', 'required' => true, 'order' => 1],
            ['subject_id' => 12, 'method' => 'level_with_score', 'required' => true, 'order' => 2],
            ['subject_id' => 3, 'method' => 'level_with_score', 'required' => true, 'order' => 3],
            ['subject_id' => 14, 'method' => 'level_with_score', 'required' => true, 'order' => 4],
            ['subject_id' => 15, 'method' => 'level_with_score', 'required' => true, 'order' => 5],
            // Level only
            ['subject_id' => 4, 'method' => 'level', 'required' => true, 'order' => 6],
            ['subject_id' => 5, 'method' => 'level', 'required' => true, 'order' => 7],
            ['subject_id' => 9, 'method' => 'level', 'required' => true, 'order' => 8],
            ['subject_id' => 26, 'method' => 'level', 'required' => true, 'order' => 9],
            ['subject_id' => 27, 'method' => 'level', 'required' => true, 'order' => 10],
            ['subject_id' => 11, 'method' => 'level', 'required' => true, 'order' => 11],
        ];

        // Classes 4-5 (ids: 4, 5)
        foreach ([4, 5] as $classId) {
            $configs[$classId] = [
                // Level with score
                ['subject_id' => 2, 'method' => 'level_with_score', 'required' => true, 'order' => 1],
                ['subject_id' => 12, 'method' => 'level_with_score', 'required' => true, 'order' => 2],
                ['subject_id' => 3, 'method' => 'level_with_score', 'required' => true, 'order' => 3],
                ['subject_id' => 6, 'method' => 'level_with_score', 'required' => true, 'order' => 4], // Lịch sử và Địa lí
                ['subject_id' => 7, 'method' => 'level_with_score', 'required' => true, 'order' => 5], // Khoa học
                ['subject_id' => 14, 'method' => 'level_with_score', 'required' => true, 'order' => 6],
                ['subject_id' => 15, 'method' => 'level_with_score', 'required' => true, 'order' => 7],
                // Level only
                ['subject_id' => 4, 'method' => 'level', 'required' => true, 'order' => 8],
                ['subject_id' => 9, 'method' => 'level', 'required' => true, 'order' => 9],
                ['subject_id' => 26, 'method' => 'level', 'required' => true, 'order' => 10],
                ['subject_id' => 27, 'method' => 'level', 'required' => true, 'order' => 11],
                ['subject_id' => 11, 'method' => 'level', 'required' => true, 'order' => 12],
            ];
        }

        // Classes 6-9 (ids: 6, 7, 8, 9)
        foreach ([6, 7, 8, 9] as $classId) {
            $configs[$classId] = [
                // Score
                ['subject_id' => 13, 'method' => 'score', 'required' => true, 'order' => 1], // Ngữ văn
                ['subject_id' => 2, 'method' => 'score', 'required' => true, 'order' => 2],  // Toán
                ['subject_id' => 3, 'method' => 'score', 'required' => true, 'order' => 3],  // Ngoại ngữ 1
                ['subject_id' => 17, 'method' => 'score', 'required' => true, 'order' => 4], // Khoa học tự nhiên
                ['subject_id' => 6, 'method' => 'score', 'required' => true, 'order' => 5],  // Lịch sử và Địa lí
                ['subject_id' => 14, 'method' => 'score', 'required' => true, 'order' => 6], // Tin học
                ['subject_id' => 15, 'method' => 'score', 'required' => true, 'order' => 7], // Công nghệ
                // Comment
                ['subject_id' => 9, 'method' => 'comment', 'required' => true, 'order' => 8], // GD thể chất
                ['subject_id' => 26, 'method' => 'comment', 'required' => true, 'order' => 9], // Âm nhạc
                ['subject_id' => 27, 'method' => 'comment', 'required' => true, 'order' => 10], // Mĩ thuật
                ['subject_id' => 16, 'method' => 'comment', 'required' => true, 'order' => 11], // HĐ trải nghiệm, hướng nghiệp
            ];
        }

        // Classes 10-12 (ids: 10, 11, 12)
        foreach ([10, 11, 12] as $classId) {
            $configs[$classId] = [
                // Compulsory (8 subjects): 13, 2, 3, 19, 21 (score), 9, 16, 18 (comment)
                ['subject_id' => 13, 'method' => 'score', 'required' => true, 'order' => 1],  // Ngữ văn
                ['subject_id' => 2, 'method' => 'score', 'required' => true, 'order' => 2],   // Toán
                ['subject_id' => 3, 'method' => 'score', 'required' => true, 'order' => 3],   // Ngoại ngữ 1
                ['subject_id' => 19, 'method' => 'score', 'required' => true, 'order' => 4],  // Lịch sử
                ['subject_id' => 21, 'method' => 'score', 'required' => true, 'order' => 5],  // GDQPAN
                ['subject_id' => 9, 'method' => 'comment', 'required' => true, 'order' => 6], // GD thể chất
                ['subject_id' => 16, 'method' => 'comment', 'required' => true, 'order' => 7], // HĐ trải nghiệm, hướng nghiệp
                ['subject_id' => 18, 'method' => 'comment', 'required' => true, 'order' => 8], // ND GD địa phương

                // Electives (is_required = false): 14, 15, 20, 22, 23, 24, 25, 26, 27
                ['subject_id' => 20, 'method' => 'score', 'required' => false, 'order' => 9],  // Địa lý
                ['subject_id' => 22, 'method' => 'score', 'required' => false, 'order' => 10], // GDKT&PL
                ['subject_id' => 23, 'method' => 'score', 'required' => false, 'order' => 11], // Vật lí
                ['subject_id' => 24, 'method' => 'score', 'required' => false, 'order' => 12], // Hóa học
                ['subject_id' => 25, 'method' => 'score', 'required' => false, 'order' => 13], // Sinh học
                ['subject_id' => 14, 'method' => 'score', 'required' => false, 'order' => 14], // Tin học
                ['subject_id' => 15, 'method' => 'score', 'required' => false, 'order' => 15], // Công nghệ
                ['subject_id' => 26, 'method' => 'comment', 'required' => false, 'order' => 16], // Âm nhạc
                ['subject_id' => 27, 'method' => 'comment', 'required' => false, 'order' => 17], // Mĩ thuật
            ];
        }

        foreach ($configs as $classId => $subjects) {
            foreach ($subjects as $sub) {
                DB::table('class_subject')->updateOrInsert(
                    [
                        'class_id' => $classId,
                        'subject_id' => $sub['subject_id'],
                    ],
                    [
                        'evaluation_method' => $sub['method'],
                        'is_required' => $sub['required'],
                        'sort_order' => $sub['order'],
                    ]
                );
            }
        }
    }
}
