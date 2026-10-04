<?php

namespace Tests\Feature\ParentRank;

use App\Enums\DeleteStatus;
use App\Models\User;
use App\Models\UserDailyActivity;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ParentRankApiTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->orderBy('id')->firstOrFail();
    }

    private function authHeaders(User $user): array
    {
        $this->app['auth']->forgetGuards();
        auth('api')->forgetUser();
        try {
            JWTAuth::unsetToken();
        } catch (\Throwable $e) {}

        $token = JWTAuth::fromUser($user);
        UserSession::create([
            'user_id' => $user->id,
            'access_token' => $token,
            'status' => DeleteStatus::NotDeleted,
        ]);

        return [
            'Authorization' => 'Bearer ' . $token,
            'X-TOKEN-ACCESS' => config('custom_api.X-TOKEN-ACCESS'),
            'Accept' => 'application/json',
        ];
    }

    /**
     * Khách chưa đăng nhập không thể truy cập /parent-rank/me
     */
    public function test_guest_cannot_access_parent_rank_me(): void
    {
        $response = $this->getJson('/api/v1/parent-rank/me', [
            'X-TOKEN-ACCESS' => config('custom_api.X-TOKEN-ACCESS'),
        ]);

        $response->assertStatus(401);
    }

    /**
     * API tiers công khai (yêu cầu X-TOKEN-ACCESS) trả về danh sách 5 cấp bậc
     */
    public function test_get_parent_rank_tiers(): void
    {
        $response = $this->getJson('/api/v1/parent-rank/tiers', [
            'X-TOKEN-ACCESS' => config('custom_api.X-TOKEN-ACCESS'),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'is_active',
                'weights' => ['usage', 'frequency', 'assessment', 'child_score'],
                'targets' => ['usage_minutes', 'active_days', 'assessments'],
                'tiers' => [
                    '*' => ['value', 'name', 'color', 'icon', 'badge', 'min_points', 'description'],
                ],
            ],
        ]);

        $this->assertCount(5, $response->json('data.tiers'));
    }

    /**
     * API /parent-rank/me trả về hạng và chi tiết 4 tiêu chí của người dùng đăng nhập
     */
    public function test_get_parent_rank_me_authenticated(): void
    {
        $headers = $this->authHeaders($this->user);

        $response = $this->getJson('/api/v1/parent-rank/me', $headers);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'period',
                'is_final',
                'rank' => ['value', 'name', 'badge', 'color', 'icon', 'min_points'],
                'total_points',
                'progress_percent',
                'suggestion',
                'breakdown' => [
                    '*' => ['key', 'label', 'raw', 'unit', 'target', 'score', 'weight'],
                ],
                'calculated_at',
            ],
        ]);

        // Phải có đủ 4 tiêu chí đánh giá
        $breakdownKeys = collect($response->json('data.breakdown'))->pluck('key')->all();
        $this->assertContains('usage', $breakdownKeys);
        $this->assertContains('frequency', $breakdownKeys);
        $this->assertContains('assessment', $breakdownKeys);
        $this->assertContains('child_score', $breakdownKeys);
    }

    /**
     * API /parent-rank/history trả về danh sách lịch sử
     */
    public function test_get_parent_rank_history(): void
    {
        $headers = $this->authHeaders($this->user);

        $response = $this->getJson('/api/v1/parent-rank/history', $headers);

        $response->assertStatus(200);
        $this->assertIsArray($response->json('data'));
    }

    /**
     * API POST /tracking/app-session ghi nhận thời gian dùng app vào user_daily_activities
     */
    public function test_app_session_tracking_record_heartbeat(): void
    {
        $headers = $this->authHeaders($this->user);

        $today = now()->toDateString();
        $initialActivity = UserDailyActivity::where('user_id', $this->user->id)
            ->where('activity_date', $today)
            ->first();

        $initialSeconds = $initialActivity ? $initialActivity->active_seconds : 0;

        $response = $this->postJson('/api/v1/tracking/app-session', [
            'event' => 'heartbeat',
            'seconds' => 60,
        ], $headers);

        $response->assertStatus(200);

        $updatedActivity = UserDailyActivity::where('user_id', $this->user->id)
            ->where('activity_date', $today)
            ->first();

        $this->assertNotNull($updatedActivity);
        $this->assertGreaterThanOrEqual($initialSeconds + 60, $updatedActivity->active_seconds);
    }

    /**
     * API POST /tracking/app-session kiểm tra validation lỗi
     */
    public function test_app_session_tracking_validation_error(): void
    {
        $headers = $this->authHeaders($this->user);

        $response = $this->postJson('/api/v1/tracking/app-session', [
            'event' => 'invalid_event',
            'seconds' => -10,
        ], $headers);

        $response->assertStatus(422);
    }

    /**
     * Kiểm tra Artisan command parent-rank:calculate ở chế độ dry-run chạy không lỗi
     */
    public function test_artisan_calculate_command_dry_run(): void
    {
        $this->artisan('parent-rank:calculate', [
            '--period' => now()->format('Y-m'),
            '--user_id' => $this->user->id,
            '--dry-run' => true,
        ])->assertExitCode(0);
    }
}
