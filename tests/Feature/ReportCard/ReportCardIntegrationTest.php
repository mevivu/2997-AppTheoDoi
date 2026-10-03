<?php

namespace Tests\Feature\ReportCard;

use App\Api\V1\Http\Requests\ChildEvaluation\ChildEvaluationRequest;
use App\Api\V1\Services\ChildEvaluation\ChildEvaluationService;
use App\Enums\ActiveStatus;
use App\Enums\ChildEvaluation\AcademicRating;
use App\Enums\ChildEvaluation\ConductRating;
use App\Enums\Class\LevelGroup;
use App\Enums\Semester\SemesterStatus;
use App\Models\Child;
use App\Models\ChildEvaluation;
use App\Models\ClassGrade;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportCardIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private Child $child;
    private SchoolClass $class;
    private ClassGrade $classGrade;
    private ChildEvaluation $evaluation;
    private ChildEvaluationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->child = Child::query()->whereNotNull('user_id')->first();
        if (!$this->child) {
            $this->markTestSkipped('Không có dữ liệu child.');
        }

        $this->user = User::findOrFail($this->child->user_id);
        $this->class = SchoolClass::find(6); // Lớp 6 (lower_secondary)
        $this->classGrade = ClassGrade::firstOrCreate([
            'child_id' => $this->child->id,
            'class_id' => $this->class->id,
        ], [
            'status' => ActiveStatus::Active,
        ]);

        $this->evaluation = ChildEvaluation::firstOrCreate([
            'class_grade_id' => $this->classGrade->id,
            'semester' => SemesterStatus::Semester1,
        ], [
            'status' => ActiveStatus::Active,
            'conduct' => ConductRating::Good,
            'academic_performance' => AcademicRating::Fair,
        ]);

        $this->service = app(ChildEvaluationService::class);
    }

    private function getPayloadWithSubjects(float $mathGrade = 8.5): array
    {
        // 7 score subjects (2, 13, 3, 17, 6, 14, 15), 4 comment subjects (9, 26, 27, 16)
        $scoreIds = [2, 13, 3, 17, 6, 14, 15];
        $commentIds = [9, 26, 27, 16];

        $subjects = [];
        foreach ($scoreIds as $id) {
            $subjects[] = [
                'id' => $id,
                'grade' => ($id === 2) ? $mathGrade : 8.5,
                'remark' => 'Tốt',
            ];
        }

        foreach ($commentIds as $id) {
            $subjects[] = [
                'id' => $id,
                'achievement_level' => 'achieved',
                'remark' => 'Đạt',
            ];
        }

        return [
            'child_evaluation_id' => $this->evaluation->id,
            'status' => ActiveStatus::Active->value,
            'conduct' => ConductRating::Good->value,
            'academic_performance' => AcademicRating::Fair->value,
            'subjects' => $subjects,
            'qualities' => [],
            'capabilities' => [],
        ];
    }

    public function test_when_flags_off_calculated_columns_remain_null(): void
    {
        Config::set('report_card.engine_enabled', false);
        Config::set('report_card.auto_classification', false);

        $payload = $this->getPayloadWithSubjects(8.5);
        $request = ChildEvaluationRequest::create('/api/v1/child-evaluations', 'PUT', $payload);
        $request->setContainer(app())->validateResolved();

        $updated = $this->service->update($request);

        $this->assertNull($updated->calculated_academic_performance);
        $this->assertSame(AcademicRating::Fair, $updated->academic_performance);
    }

    public function test_when_engine_enabled_on_and_auto_classify_off_calculates_without_overwriting_user_rating(): void
    {
        Config::set('report_card.engine_enabled', true);
        Config::set('report_card.auto_classification', false);

        $payload = $this->getPayloadWithSubjects(8.5);
        $request = ChildEvaluationRequest::create('/api/v1/child-evaluations', 'PUT', $payload);
        $request->setContainer(app())->validateResolved();

        $updated = $this->service->update($request);

        $this->assertSame('good', $updated->calculated_academic_performance);
        $this->assertSame('ok', $updated->calculation_status->value);
        $this->assertNotNull($updated->calculation_snapshot);
        // User's rating is preserved
        $this->assertSame(AcademicRating::Fair, $updated->academic_performance);
    }

    public function test_when_both_flags_on_updates_academic_performance_to_calculated(): void
    {
        Config::set('report_card.engine_enabled', true);
        Config::set('report_card.auto_classification', true);

        $payload = $this->getPayloadWithSubjects(8.5);
        $request = ChildEvaluationRequest::create('/api/v1/child-evaluations', 'PUT', $payload);
        $request->setContainer(app())->validateResolved();

        $updated = $this->service->update($request);

        $this->assertSame('good', $updated->calculated_academic_performance);
        $this->assertSame(AcademicRating::Good, $updated->academic_performance);
    }

    public function test_override_flag_preserves_user_rating_even_when_both_flags_on(): void
    {
        Config::set('report_card.engine_enabled', true);
        Config::set('report_card.auto_classification', true);

        $payload = $this->getPayloadWithSubjects(8.5);
        $payload['override_academic_performance'] = true;
        $payload['academic_performance'] = AcademicRating::Fair->value;

        $request = ChildEvaluationRequest::create('/api/v1/child-evaluations', 'PUT', $payload);
        $request->setContainer(app())->validateResolved();

        $updated = $this->service->update($request);

        $this->assertTrue($updated->is_performance_overridden);
        $this->assertSame('good', $updated->calculated_academic_performance);
        // User's overridden rating is kept
        $this->assertSame(AcademicRating::Fair, $updated->academic_performance);
    }
}
