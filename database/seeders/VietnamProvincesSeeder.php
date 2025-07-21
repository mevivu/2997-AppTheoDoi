<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class VietnamProvincesSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch data from API
        $response = Http::get('https://vietnamlabs.com/api/vietnamprovince');

        if (!$response->ok()) {
            $this->command->error('Failed to fetch data from API.');
            return;
        }

        $data = $response->json();

        if (!$data['success'] || empty($data['data'])) {
            $this->command->error('API returned empty or invalid data.');
            return;
        }

        foreach ($data['data'] as $provinceData) {
            // Insert province
            $provinceId = (int) $provinceData['id'];
            DB::table('provinces')->updateOrInsert(
                ['id' => $provinceId],
                ['name' => $provinceData['province']]
            );

            // Insert wards
            foreach ($provinceData['wards'] as $ward) {
                DB::table('wards')->updateOrInsert(
                    [
                        'name' => $ward['name'],
                        'province_id' => $provinceId
                    ],
                    [
                        'name' => $ward['name'],
                        'province_id' => $provinceId
                    ]
                );
            }
        }

        $this->command->info('Vietnam provinces and wards seeded successfully.');
    }
}
