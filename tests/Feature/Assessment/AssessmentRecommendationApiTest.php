<?php

namespace Tests\Feature\Assessment;

use App\Enums\ActiveStatus;
use App\Enums\DeleteStatus;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Question\QuestionType;
use App\Enums\VerifiedStatus;
use App\Models\Child;
use App\Models\Lesson;
use App\Models\LessonCategory;
use App\Models\Rating;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AssessmentRecommendationApiTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Child $child;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->orderBy('id')->firstOrFail();
        $this->child = Child::create([
            'user_id' => $this->user->id,
            'fullname' => 'Bé Minh',
            'birthday' => now()->subYears(3)->format('Y-m-d'),
            'gender' => \App\Enums\User\Gender::Male,
            'is_born' => \App\Enums\Child\BornStatus::Born,
        ]);
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

    public function test_recommendation_is_generated_for_weak_categories(): void
    {
        $category = LessonCategory::firstOrCreate(
            ['pillar' => 'eq', 'key' => 'emotion_recognition'],
            [
                'name' => 'Nhận biết cảm xúc',
                'status' => ActiveStatus::Active->value,
            ]
        );

        Lesson::firstOrCreate(
            ['lesson_category_id' => $category->id, 'name' => 'Bài học nhận biết cảm xúc cơ bản'],
            [
                'status' => ActiveStatus::Active->value,
                'access_type' => LessonAccessType::Free->value,
                'difficulty' => 'easy',
            ]
        );

        $rating = Rating::create([
            'child_id' => $this->child->id,
            'type' => QuestionType::EQ->value,
            'score' => '4.5',
            'status' => VerifiedStatus::Active->value,
            'social_awareness' => '4', // <= 6 -> weak
            'self_regulation' => '8',
            'decision_making' => '8',
            'relationship_management' => '8',
            'optimism' => '8',
        ]);

        $headers = $this->authHeaders($this->user);
        $response = $this->withHeaders($headers)
            ->getJson("/api/v1/ratings/{$rating->id}/recommendations");

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertNotNull($data);
        $this->assertEquals('eq', $data['type']);
        $this->assertNotEmpty($data['parent_action']);
        $this->assertTrue($data['recommendation']['has_weak']);
        $this->assertStringContainsString('Bé Minh', $data['recommendation']['message']);
        $this->assertStringContainsString('Nhận thức cảm xúc', $data['recommendation']['message']);
        $this->assertNotEmpty($data['recommendation']['lessons']);
    }

    public function test_user_cannot_access_recommendation_of_another_users_child(): void
    {
        $otherUser = User::query()->where('id', '!=', $this->user->id)->first();
        if (!$otherUser) {
            $this->markTestSkipped('Cần ít nhất 2 users trong DB để test quyền truy cập.');
        }

        $otherChild = Child::create([
            'user_id' => $otherUser->id,
            'fullname' => 'Bé Khác',
            'birthday' => now()->subYears(2)->format('Y-m-d'),
            'gender' => \App\Enums\User\Gender::Female,
            'is_born' => \App\Enums\Child\BornStatus::Born,
        ]);

        $rating = Rating::create([
            'child_id' => $otherChild->id,
            'type' => QuestionType::AQ->value,
            'score' => '3.0',
            'status' => VerifiedStatus::Active->value,
        ]);

        $headers = $this->authHeaders($this->user);
        $response = $this->withHeaders($headers)
            ->getJson("/api/v1/ratings/{$rating->id}/recommendations");

        $response->assertStatus(403);
    }
}
