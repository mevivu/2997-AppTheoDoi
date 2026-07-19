<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppVersionTest extends TestCase
{
    public function test_it_can_check_app_version()
    {
        $response = $this->withHeaders([
            'X-TOKEN-ACCESS' => 'ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7'
        ])->getJson('/api/v1/app-versions/check?platform=android&app_type=user&current_version=1.0.0');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'notify',
                    'required',
                    'checking_version',
                    'update_url',
                    'release_notes',
                ]
            ])
            ->assertJsonFragment([
                'notify' => '1.0.0',
                'required' => '1.0.0',
            ]);
    }
}
