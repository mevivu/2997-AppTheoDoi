<?php

namespace Tests\Feature\ReportCard;

use App\Enums\ActiveStatus;
use App\Enums\ChildEvaluation\AcademicRating;
use App\Enums\ChildEvaluation\ConductRating;
use App\Enums\DeleteStatus;
use App\Enums\Semester\SemesterStatus;
use App\Models\Child;
use App\Models\ChildEvaluation;
use App\Models\ChildEvaluationAttachment;
use App\Models\ClassGrade;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportCardApiTest extends TestCase
{
    use DatabaseTransactions;

    private User $userA;
    private Child $childA;
    private User $userB;
    private Child $childB;
    private SchoolClass $class;
    private ClassGrade $classGradeA;
    private ChildEvaluation $evaluationA;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->childA = Child::query()->whereNotNull('user_id')->orderBy('id')->first();
        if (!$this->childA) {
            $this->markTestSkipped('Không có dữ liệu child.');
        }
        $this->userA = User::findOrFail($this->childA->user_id);

        $this->childB = Child::query()->whereNotNull('user_id')
            ->where('user_id', '!=', $this->childA->user_id)
            ->orderBy('id')
            ->first();
        if (!$this->childB) {
            $this->markTestSkipped('Cần ít nhất 2 user có trẻ.');
        }
        $this->userB = User::findOrFail($this->childB->user_id);

        $this->class = SchoolClass::find(6); // Lớp 6
        $this->classGradeA = ClassGrade::firstOrCreate([
            'child_id' => $this->childA->id,
            'class_id' => $this->class->id,
        ], [
            'status' => ActiveStatus::Active,
        ]);

        $this->evaluationA = ChildEvaluation::firstOrCreate([
            'class_grade_id' => $this->classGradeA->id,
            'semester' => SemesterStatus::Semester1,
        ], [
            'status' => ActiveStatus::Active,
            'conduct' => ConductRating::Good,
            'academic_performance' => AcademicRating::Fair,
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

    public function test_get_info_returns_evaluation_method_and_calculation_columns(): void
    {
        $response = $this->getJson('/api/v1/child-evaluations/info?' . http_build_query([
            'class_id' => $this->class->id,
            'child_id' => $this->childA->id,
            'semester' => SemesterStatus::Semester1->value,
        ]), $this->authHeaders($this->userA));

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertArrayHasKey('system', $data);
        $this->assertArrayHasKey('child_evaluation', $data);

        // Kiểm tra system.subjects có trường mới
        $firstSubject = $data['system']['subjects'][0] ?? null;
        $this->assertNotNull($firstSubject);
        $this->assertArrayHasKey('evaluation_method', $firstSubject);
        $this->assertArrayHasKey('is_required', $firstSubject);

        // Kiểm tra child_evaluation có các cột tính toán và attachments
        $evalData = $data['child_evaluation'];
        $this->assertArrayHasKey('calculation_status', $evalData);
        $this->assertArrayHasKey('calculated_academic_performance', $evalData);
        $this->assertArrayHasKey('is_performance_overridden', $evalData);
        $this->assertArrayHasKey('teacher_remark', $evalData);
        $this->assertArrayHasKey('attachments', $evalData);
    }

    public function test_summary_returns_stages_and_statistics(): void
    {
        $response = $this->getJson(
            '/api/v1/child-evaluations/summary?child_id=' . $this->childA->id,
            $this->authHeaders($this->userA)
        );

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertArrayHasKey('stages', $data);
        $this->assertCount(3, $data['stages']);
        $this->assertEquals('primary', $data['stages'][0]['education_level']);
        $this->assertEquals('lower_secondary', $data['stages'][1]['education_level']);
        $this->assertEquals('upper_secondary', $data['stages'][2]['education_level']);
        $this->assertArrayHasKey('highlights', $data);
    }

    public function test_summary_denies_other_users_child(): void
    {
        $response = $this->getJson(
            '/api/v1/child-evaluations/summary?child_id=' . $this->childB->id,
            $this->authHeaders($this->userA)
        );
        $response->assertStatus(403);
    }

    public function test_upload_attachments_and_signed_url_streaming(): void
    {
        $file1 = UploadedFile::fake()->image('report_card_front.jpg', 800, 600);
        $file2 = UploadedFile::fake()->image('report_card_back.png', 800, 600);

        $uploadResponse = $this->postJson(
            "/api/v1/child-evaluations/{$this->evaluationA->id}/attachments",
            ['files' => [$file1, $file2]],
            $this->authHeaders($this->userA)
        );

        $uploadResponse->assertStatus(200);
        $attachments = $uploadResponse->json('data');
        $this->assertIsArray($attachments);
        $this->assertCount(2, $attachments);

        $attachmentId = $attachments[0]['id'];
        $signedUrl = $attachments[0]['url'];
        $this->assertNotEmpty($signedUrl);

        // Kiểm tra truy cập qua signed URL (không cần JWT auth và không cần X-TOKEN-ACCESS)
        $streamResponse = $this->get($signedUrl);
        $streamResponse->assertStatus(200);

        // Kiểm tra truy cập bằng URL giả mạo / thiếu signature -> 403
        $tamperedUrl = preg_replace('/signature=[^&]+/', 'signature=invalid', $signedUrl);
        $tamperedResponse = $this->get($tamperedUrl);
        $tamperedResponse->assertStatus(403);
    }

    public function test_upload_attachments_rejects_unauthorized_user(): void
    {
        // User B không thể upload vào evaluation của User A
        $file = UploadedFile::fake()->image('hack.jpg');
        $response = $this->postJson(
            "/api/v1/child-evaluations/{$this->evaluationA->id}/attachments",
            ['files' => [$file]],
            $this->authHeaders($this->userB)
        );

        $response->assertStatus(403);
    }

    public function test_delete_attachment_by_owner(): void
    {
        $file = UploadedFile::fake()->image('to_delete.jpg');
        $uploadResponse = $this->postJson(
            "/api/v1/child-evaluations/{$this->evaluationA->id}/attachments",
            ['files' => [$file]],
            $this->authHeaders($this->userA)
        );
        $attachmentId = $uploadResponse->json('data.0.id');
        $this->assertNotNull($attachmentId);

        // Xóa tệp
        $deleteResponse = $this->deleteJson(
            "/api/v1/child-evaluations/{$this->evaluationA->id}/attachments/{$attachmentId}",
            [],
            $this->authHeaders($this->userA)
        );
        $deleteResponse->assertStatus(200);

        $this->assertDatabaseMissing('child_evaluation_attachments', [
            'id' => $attachmentId,
        ]);
    }

    public function test_delete_attachment_rejects_unauthorized_user(): void
    {
        $attachment = ChildEvaluationAttachment::create([
            'child_evaluation_id' => $this->evaluationA->id,
            'disk' => 'local',
            'file_path' => 'report_cards/protected.jpg',
            'original_name' => 'protected.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 1024,
            'sort_order' => 1,
            'uploaded_by' => $this->userA->id,
        ]);

        // User B thử xóa attachment của User A -> 403 Forbidden
        $deleteResponse = $this->deleteJson(
            "/api/v1/child-evaluations/{$this->evaluationA->id}/attachments/{$attachment->id}",
            [],
            $this->authHeaders($this->userB)
        );
        $deleteResponse->assertStatus(403);
    }

    public function test_reorder_attachments(): void
    {
        $file1 = UploadedFile::fake()->image('p1.jpg');
        $file2 = UploadedFile::fake()->image('p2.jpg');

        $uploadResponse = $this->postJson(
            "/api/v1/child-evaluations/{$this->evaluationA->id}/attachments",
            ['files' => [$file1, $file2]],
            $this->authHeaders($this->userA)
        );
        $att1 = $uploadResponse->json('data.0.id');
        $att2 = $uploadResponse->json('data.1.id');

        // Đổi thứ tự: att2 lên trước, att1 về sau
        $reorderResponse = $this->patchJson(
            "/api/v1/child-evaluations/{$this->evaluationA->id}/attachments/order",
            [
                'order' => [
                    ['id' => $att2, 'sort_order' => 1],
                    ['id' => $att1, 'sort_order' => 2],
                ],
            ],
            $this->authHeaders($this->userA)
        );

        $reorderResponse->assertStatus(200);
        $ordered = $reorderResponse->json('data');
        $this->assertEquals($att2, $ordered[0]['id']);
        $this->assertEquals($att1, $ordered[1]['id']);
    }
}
