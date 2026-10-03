<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Models\FetalGrowthStandard;
use Illuminate\Database\Seeder;

class FetalGrowthStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $standards = [
            ['week' => 8,  'length' => 1.6,  'weight' => 1],
            ['week' => 9,  'length' => 2.3,  'weight' => 2],
            ['week' => 10, 'length' => 3.1,  'weight' => 4],
            ['week' => 11, 'length' => 4.1,  'weight' => 45],
            ['week' => 12, 'length' => 5.4,  'weight' => 58],
            ['week' => 13, 'length' => 7.4,  'weight' => 73],
            ['week' => 14, 'length' => 8.7,  'weight' => 93],
            ['week' => 15, 'length' => 10.1, 'weight' => 117],
            ['week' => 16, 'length' => 11.6, 'weight' => 146],
            ['week' => 17, 'length' => 13.0, 'weight' => 181],
            ['week' => 18, 'length' => 14.2, 'weight' => 222],
            ['week' => 19, 'length' => 15.3, 'weight' => 272],
            ['week' => 20, 'length' => 25.6, 'weight' => 330],
            ['week' => 21, 'length' => 26.7, 'weight' => 400],
            ['week' => 22, 'length' => 27.8, 'weight' => 476],
            ['week' => 23, 'length' => 28.9, 'weight' => 565],
            ['week' => 24, 'length' => 30.0, 'weight' => 665],
            ['week' => 25, 'length' => 34.6, 'weight' => 756],
            ['week' => 26, 'length' => 35.6, 'weight' => 900],
            ['week' => 27, 'length' => 36.6, 'weight' => 1000],
            ['week' => 28, 'length' => 37.6, 'weight' => 1100],
            ['week' => 29, 'length' => 38.6, 'weight' => 1239],
            ['week' => 30, 'length' => 39.9, 'weight' => 1396],
            ['week' => 31, 'length' => 41.1, 'weight' => 1568],
            ['week' => 32, 'length' => 42.4, 'weight' => 1755],
            ['week' => 33, 'length' => 43.7, 'weight' => 2000],
            ['week' => 34, 'length' => 45.0, 'weight' => 2200],
            ['week' => 35, 'length' => 46.2, 'weight' => 2378],
            ['week' => 36, 'length' => 47.4, 'weight' => 2600],
            ['week' => 37, 'length' => 48.6, 'weight' => 2800],
            ['week' => 38, 'length' => 49.8, 'weight' => 3000],
            ['week' => 39, 'length' => 50.7, 'weight' => 3186],
            ['week' => 40, 'length' => 51.2, 'weight' => 3338],
            ['week' => 41, 'length' => 51.7, 'weight' => 3600],
            ['week' => 42, 'length' => 51.7, 'weight' => 3700],
        ];

        foreach ($standards as $item) {
            FetalGrowthStandard::updateOrCreate(
                ['week' => $item['week']],
                [
                    'length' => $item['length'],
                    'weight' => $item['weight'],
                    'status' => ActiveStatus::Active->value,
                ]
            );
        }
    }
}
