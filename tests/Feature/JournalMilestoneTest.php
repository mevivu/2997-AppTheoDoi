<?php

namespace Tests\Feature;

use App\Enums\Child\BornStatus;
use App\Enums\Journal\JournalType;
use App\Enums\Journal\MilestoneStage;
use App\Models\Child;
use App\Models\Journal;
use App\Models\JournalMilestone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JournalMilestoneTest extends TestCase
{
    use DatabaseTransactions;

    protected User $userA;
    protected User $userB;
    protected Child $childA;
    protected Child $childB;
    protected JournalMilestone $milestone1;
    protected JournalMilestone $milestone2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'X-TOKEN-ACCESS' => config('custom_api.X-TOKEN-ACCESS'),
        ]);

        $this->userA = User::create([
            'fullname' => 'User A',
            'code' => 'USR_' . rand(100000, 999999),
            'phone' => '0987' . rand(100000, 999999),
            'email' => 'usera_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->userB = User::create([
            'fullname' => 'User B',
            'code' => 'USR_' . rand(100000, 999999),
            'phone' => '0987' . rand(100000, 999999),
            'email' => 'userb_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->childA = Child::create([
            'user_id' => $this->userA->id,
            'fullname' => 'Bé A',
            'birthday' => Carbon::now()->subMonths(6)->toDateString(), // 6 tháng -> newborn
            'is_born' => BornStatus::Born,
        ]);

        $this->childB = Child::create([
            'user_id' => $this->userB->id,
            'fullname' => 'Bé B',
            'birthday' => Carbon::now()->subMonths(20)->toDateString(), // 20 tháng -> toddler
            'is_born' => BornStatus::Born,
        ]);

        $this->milestone1 = JournalMilestone::firstOrCreate(
            ['slug' => 'test-milestone-1'],
            [
                'stage' => MilestoneStage::Newborn,
                'name' => 'Biết lẫy test',
                'age_from_month' => 0,
                'age_to_month' => 12,
                'position' => 1,
                'is_active' => true,
            ]
        );

        $this->milestone2 = JournalMilestone::firstOrCreate(
            ['slug' => 'test-milestone-2'],
            [
                'stage' => MilestoneStage::Toddler,
                'name' => 'Biết đi test',
                'age_from_month' => 12,
                'age_to_month' => 36,
                'position' => 2,
                'is_active' => true,
            ]
        );
    }

    /**
     * 1. GET /api/v1/journal-milestones trả về đúng suggested_stage theo ngày sinh của trẻ
     */
    public function test_get_journal_milestones_returns_correct_stages_and_suggested_stage(): void
    {
        $response = $this->actingAs($this->userA, 'api')
            ->getJson("/api/v1/journal-milestones?child_id={$this->childA->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.suggested_stage', 'newborn');
        $this->assertNotEmpty($response->json('data.stages'));
    }

    /**
     * 2. User A không thể lấy milestones của con User B (ChildBelongsToUser rule)
     */
    public function test_user_cannot_access_milestones_of_another_users_child(): void
    {
        $response = $this->actingAs($this->userA, 'api')
            ->getJson("/api/v1/journal-milestones?child_id={$this->childB->id}");

        // Validation rule ChildBelongsToUser fails -> BaseRequest returns 400
        $response->assertStatus(400);
    }

    /**
     * 3. Tạo nhật ký không ảnh thành công, không bị crash 500 (Fix B2)
     */
    public function test_create_journal_without_image_succeeds(): void
    {
        $payload = [
            'child_id' => $this->childA->id,
            'title' => 'Khoảnh khắc đầu tiên',
            'content' => 'Con rất ngoan và đáng yêu',
            'type' => JournalType::Moment->value,
            'milestone_id' => $this->milestone1->id,
        ];

        $response = $this->actingAs($this->userA, 'api')
            ->postJson('/api/v1/journals', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('journals', [
            'child_id' => $this->childA->id,
            'milestone_id' => $this->milestone1->id,
            'title' => 'Khoảnh khắc đầu tiên',
        ]);
    }

    /**
     * 4. Tạo 2 lần cùng một cột mốc cho cùng 1 trẻ sẽ bị chặn (400)
     */
    public function test_cannot_create_duplicate_milestone_for_same_child(): void
    {
        Journal::create([
            'child_id' => $this->childA->id,
            'milestone_id' => $this->milestone1->id,
            'title' => 'Lần 1',
            'content' => 'Nội dung',
            'type' => JournalType::Moment->value,
        ]);

        $payload = [
            'child_id' => $this->childA->id,
            'title' => 'Lần 2 trùng mốc',
            'type' => JournalType::Moment->value,
            'milestone_id' => $this->milestone1->id,
        ];

        $response = $this->actingAs($this->userA, 'api')
            ->postJson('/api/v1/journals', $payload);

        $response->assertStatus(400);
        $this->assertStringContainsString('Bé đã có nhật ký cho cột mốc này', json_encode($response->json(), JSON_UNESCAPED_UNICODE));
    }

    /**
     * 5. milestone_id không được phép gắn với type = prescription (400)
     */
    public function test_milestone_cannot_be_used_with_prescription_type(): void
    {
        $payload = [
            'child_id' => $this->childA->id,
            'title' => 'Toa thuốc',
            'content' => 'Kháng sinh',
            'type' => JournalType::Prescription->value,
            'milestone_id' => $this->milestone1->id,
        ];

        $response = $this->actingAs($this->userA, 'api')
            ->postJson('/api/v1/journals', $payload);

        $response->assertStatus(400);
    }

    /**
     * 6. Danh sách nhật ký sắp xếp theo ngày sự kiện (created_at desc) và load milestone
     */
    public function test_journal_index_sorts_by_event_date_desc(): void
    {
        $oldDate = Carbon::now()->subDays(10)->format('Y-m-d H:i:s');
        $newDate = Carbon::now()->subDays(2)->format('Y-m-d H:i:s');

        $jOld = Journal::create([
            'child_id' => $this->childA->id,
            'title' => 'Sự kiện cũ',
            'content' => 'Cũ',
            'type' => JournalType::Moment->value,
            'created_at' => $oldDate,
        ]);

        $jNew = Journal::create([
            'child_id' => $this->childA->id,
            'title' => 'Sự kiện mới',
            'content' => 'Mới',
            'type' => JournalType::Moment->value,
            'created_at' => $newDate,
        ]);

        $response = $this->actingAs($this->userA, 'api')
            ->getJson("/api/v1/journals?child_id={$this->childA->id}&type=moment");

        $response->assertStatus(200);
        $journals = $response->json('data.journals');

        $this->assertNotEmpty($journals);
        $first = $journals[0];
        $this->assertEquals($jNew->id, $first['id']);
    }

    /**
     * 7. IDOR: User B không thể xem chi tiết journal của con User A (Fix B1 -> trả về 404)
     */
    public function test_user_b_cannot_view_user_a_child_journal(): void
    {
        $journalA = Journal::create([
            'child_id' => $this->childA->id,
            'title' => 'Bí mật của bé A',
            'content' => 'Không được lộ',
            'type' => JournalType::Moment->value,
        ]);

        $response = $this->actingAs($this->userB, 'api')
            ->getJson("/api/v1/journals/{$journalA->id}");

        $response->assertStatus(404);
    }

    /**
     * 8. IDOR: User B không thể xoá journal của con User A (Fix B1 -> trả về 404)
     */
    public function test_user_b_cannot_delete_user_a_child_journal(): void
    {
        $journalA = Journal::create([
            'child_id' => $this->childA->id,
            'title' => 'Nhật ký của bé A',
            'content' => 'Nội dung',
            'type' => JournalType::Moment->value,
        ]);

        $response = $this->actingAs($this->userB, 'api')
            ->deleteJson("/api/v1/journals/{$journalA->id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('journals', ['id' => $journalA->id]);
    }

    /**
     * 9. IDOR: User B không thể cập nhật journal của con User A (Fix B1 -> trả về 404)
     */
    public function test_user_b_cannot_update_user_a_child_journal(): void
    {
        $journalA = Journal::create([
            'child_id' => $this->childA->id,
            'title' => 'Nhật ký của bé A',
            'content' => 'Nội dung gốc',
            'type' => JournalType::Moment->value,
        ]);

        $response = $this->actingAs($this->userB, 'api')
            ->postJson('/api/v1/journals/update', [
                'id' => $journalA->id,
                'title' => 'Bị sửa bởi Hacker',
            ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('journals', [
            'id' => $journalA->id,
            'title' => 'Nhật ký của bé A',
        ]);
    }

    /**
     * 10. Route journals/{id} có ràng buộc số, không crash với chuỗi 'abc'
     */
    public function test_non_numeric_journal_id_returns_404(): void
    {
        $response = $this->actingAs($this->userA, 'api')
            ->getJson('/api/v1/journals/abc');

        $response->assertStatus(404);
    }

    /**
     * 11. User chưa có userPackages không bị 500 khi lấy danh sách nhật ký (Fix B3)
     */
    public function test_user_without_package_does_not_throw_500(): void
    {
        $this->userA->userPackages()->delete();
        $this->assertEquals(0, $this->userA->userPackages()->count());

        Journal::create([
            'child_id' => $this->childA->id,
            'title' => 'Nhật ký gói miễn phí',
            'content' => 'Nội dung',
            'type' => JournalType::Moment->value,
            'created_at' => Carbon::now()->subYears(2)->format('Y-m-d H:i:s'),
        ]);

        $response = $this->actingAs($this->userA, 'api')
            ->getJson("/api/v1/journals?child_id={$this->childA->id}&type=moment");

        $response->assertStatus(200);
        $this->assertFalse($response->json('data.journals.0.checked'));
    }
}
