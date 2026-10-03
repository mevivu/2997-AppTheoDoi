<?php

namespace Tests\Feature\ReportCard;

use App\Api\V1\Exception\ReportCardAccessDeniedException;
use App\Api\V1\Services\ChildEvaluation\ChildEvaluationServiceInterface;
use App\Api\V1\Services\ChildEvaluation\ReportCardAccessGuard;
use App\Models\Child;
use App\Models\ClassGrade;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * GĐ0 – Hotfix học bạ.
 *
 * Dùng DatabaseTransactions (KHÔNG dùng RefreshDatabase) vì phpunit.xml đang trỏ vào DB dev:
 * mọi thay đổi trong test đều được rollback.
 */
class ReportCardHotfixTest extends TestCase
{
    use DatabaseTransactions;

    /** @return array{0: User, 1: Child, 2: Child} [userA, childOfA, childOfOtherUser] */
    private function twoChildrenOfDifferentUsers(): array
    {
        $childA = Child::query()->whereNotNull('user_id')->orderBy('id')->first();
        if (!$childA) {
            $this->markTestSkipped('Không có dữ liệu trẻ để kiểm thử.');
        }
        $childB = Child::query()->whereNotNull('user_id')
            ->where('user_id', '!=', $childA->user_id)
            ->orderBy('id')
            ->first();
        if (!$childB) {
            $this->markTestSkipped('Cần ít nhất 2 user có trẻ.');
        }

        return [User::findOrFail($childA->user_id), $childA, $childB];
    }

    public function test_service_no_longer_depends_on_phpunit(): void
    {
        $source = file_get_contents(app_path('Api/V1/Services/ChildEvaluation/ChildEvaluationService.php'));

        $this->assertDoesNotMatchRegularExpression('/^\s*use\s+(function\s+)?PHPUnit\\\\/m', $source);
        $this->assertDoesNotMatchRegularExpression('/(?<![\w>:])isEmpty\s*\(\s*\$/', $source);
    }

    public function test_owner_can_access_own_child(): void
    {
        [$userA, $childA] = $this->twoChildrenOfDifferentUsers();
        $this->actingAs($userA, 'api');

        app(ReportCardAccessGuard::class)->assertOwnsChild($childA->id);

        $this->assertTrue(true);
    }

    public function test_user_cannot_access_other_users_child(): void
    {
        [$userA, , $childB] = $this->twoChildrenOfDifferentUsers();
        $this->actingAs($userA, 'api');

        $this->expectException(ReportCardAccessDeniedException::class);
        app(ReportCardAccessGuard::class)->assertOwnsChild($childB->id);
    }

    public function test_guest_is_denied(): void
    {
        $child = Child::query()->first();
        if (!$child) {
            $this->markTestSkipped('Không có dữ liệu trẻ.');
        }

        $this->expectException(ReportCardAccessDeniedException::class);
        app(ReportCardAccessGuard::class)->assertOwnsChild($child->id);
    }

    public function test_user_cannot_access_other_users_evaluation(): void
    {
        [$userA, $childA, $childB] = $this->twoChildrenOfDifferentUsers();
        $service = app(ChildEvaluationServiceInterface::class);
        $classId = DB::table('classes')->orderBy('id')->value('id');

        $ownEvaluation = $service->findAndCreateChildEvaluations($childA->id, $classId, 'semester_1');
        $otherEvaluation = $service->findAndCreateChildEvaluations($childB->id, $classId, 'semester_1');

        $this->actingAs($userA, 'api');
        $guard = app(ReportCardAccessGuard::class);

        $guard->assertOwnsEvaluation($ownEvaluation->id);

        $this->expectException(ReportCardAccessDeniedException::class);
        $guard->assertOwnsEvaluation($otherEvaluation->id);
    }

    public function test_find_and_create_evaluation_when_class_grade_missing(): void
    {
        $child = Child::query()->orderBy('id')->first();
        if (!$child) {
            $this->markTestSkipped('Không có dữ liệu trẻ.');
        }
        $classId = DB::table('classes')->orderBy('id')->value('id');

        // Giả lập dữ liệu cũ thiếu class_grade (trước đây gây lỗi 500).
        ClassGrade::where('child_id', $child->id)->where('class_id', $classId)->delete();

        $evaluation = app(ChildEvaluationServiceInterface::class)
            ->findAndCreateChildEvaluations($child->id, $classId, 'semester_1');

        $this->assertNotNull($evaluation);
        $this->assertSame(1, ClassGrade::where('child_id', $child->id)->where('class_id', $classId)->count());
    }

    public function test_find_and_create_evaluation_is_idempotent(): void
    {
        $child = Child::query()->orderBy('id')->first();
        if (!$child) {
            $this->markTestSkipped('Không có dữ liệu trẻ.');
        }
        $classId = DB::table('classes')->orderBy('id')->value('id');
        $service = app(ChildEvaluationServiceInterface::class);

        $first = $service->findAndCreateChildEvaluations($child->id, $classId, 'semester_2');
        $second = $service->findAndCreateChildEvaluations($child->id, $classId, 'semester_2');

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, DB::table('child_evaluations')
            ->where('class_grade_id', $first->class_grade_id)
            ->where('semester', 'semester_2')
            ->count());
    }
}
