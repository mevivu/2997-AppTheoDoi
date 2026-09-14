<?php

use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use App\Models\Bmi;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Dữ liệu chuẩn BMI (WHO Child Growth Standards) cho bé TRAI từ 1 - 5 tuổi
        $boys = [
            1 => ['z_minus_3' => 13.4, 'z_minus_2' => 14.4, 'z_minus_1' => 15.5, 'z_0' => 16.8, 'z_plus_1' => 18.2, 'z_plus_2' => 19.8, 'z_plus_3' => 21.6],
            2 => ['z_minus_3' => 12.9, 'z_minus_2' => 13.8, 'z_minus_1' => 14.8, 'z_0' => 16.0, 'z_plus_1' => 17.3, 'z_plus_2' => 18.9, 'z_plus_3' => 20.6],
            3 => ['z_minus_3' => 12.4, 'z_minus_2' => 13.4, 'z_minus_1' => 14.4, 'z_0' => 15.6, 'z_plus_1' => 16.9, 'z_plus_2' => 18.4, 'z_plus_3' => 20.0],
            4 => ['z_minus_3' => 12.1, 'z_minus_2' => 13.1, 'z_minus_1' => 14.1, 'z_0' => 15.3, 'z_plus_1' => 16.7, 'z_plus_2' => 18.2, 'z_plus_3' => 19.9],
            5 => ['z_minus_3' => 12.0, 'z_minus_2' => 12.9, 'z_minus_1' => 14.0, 'z_0' => 15.2, 'z_plus_1' => 16.6, 'z_plus_2' => 18.3, 'z_plus_3' => 20.3],
        ];

        // Dữ liệu chuẩn BMI (WHO Child Growth Standards) cho bé GÁI từ 1 - 5 tuổi
        $girls = [
            1 => ['z_minus_3' => 12.7, 'z_minus_2' => 13.8, 'z_minus_1' => 15.0, 'z_0' => 16.4, 'z_plus_1' => 17.9, 'z_plus_2' => 19.6, 'z_plus_3' => 21.6],
            2 => ['z_minus_3' => 12.4, 'z_minus_2' => 13.3, 'z_minus_1' => 14.4, 'z_0' => 15.7, 'z_plus_1' => 17.1, 'z_plus_2' => 18.7, 'z_plus_3' => 20.6],
            3 => ['z_minus_3' => 12.1, 'z_minus_2' => 13.1, 'z_minus_1' => 14.2, 'z_0' => 15.4, 'z_plus_1' => 16.8, 'z_plus_2' => 18.4, 'z_plus_3' => 20.3],
            4 => ['z_minus_3' => 11.8, 'z_minus_2' => 12.8, 'z_minus_1' => 14.0, 'z_0' => 15.3, 'z_plus_1' => 16.8, 'z_plus_2' => 18.5, 'z_plus_3' => 20.6],
            5 => ['z_minus_3' => 11.6, 'z_minus_2' => 12.7, 'z_minus_1' => 13.9, 'z_0' => 15.3, 'z_plus_1' => 16.9, 'z_plus_2' => 18.8, 'z_plus_3' => 21.1],
        ];

        foreach ($boys as $age => $row) {
            Bmi::updateOrCreate(
                [
                    'gender' => Gender::Male,
                    'age'    => $age,
                ],
                [
                    'z_score_minus_3' => $row['z_minus_3'],
                    'z_score_minus_2' => $row['z_minus_2'],
                    'z_score_minus_1' => $row['z_minus_1'],
                    'z_score_0'       => $row['z_0'],
                    'z_score_plus_1'  => $row['z_plus_1'],
                    'z_score_plus_2'  => $row['z_plus_2'],
                    'z_score_plus_3'  => $row['z_plus_3'],
                    'status'          => ActiveStatus::Active,
                ]
            );
        }

        foreach ($girls as $age => $row) {
            Bmi::updateOrCreate(
                [
                    'gender' => Gender::Female,
                    'age'    => $age,
                ],
                [
                    'z_score_minus_3' => $row['z_minus_3'],
                    'z_score_minus_2' => $row['z_minus_2'],
                    'z_score_minus_1' => $row['z_minus_1'],
                    'z_score_0'       => $row['z_0'],
                    'z_score_plus_1'  => $row['z_plus_1'],
                    'z_score_plus_2'  => $row['z_plus_2'],
                    'z_score_plus_3'  => $row['z_plus_3'],
                    'status'          => ActiveStatus::Active,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Bmi::whereIn('age', [1, 2, 3, 4])->delete();
    }
};
