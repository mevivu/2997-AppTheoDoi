<?php

namespace Tests\Feature\Classes;

use App\Admin\Http\Requests\Classes\ClassesRequest;
use App\Admin\Services\Classes\ClassesServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Class\EducationLevel;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ClassSubjectConfigTest extends TestCase
{
    use DatabaseTransactions;

    private SchoolClass $class;
    private Subject $math;
    private Subject $local;
    private Subject $civic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->class = SchoolClass::create([
            'name' => 'Lớp Test ' . uniqid(),
            'status' => ActiveStatus::Active->value,
            'level_group' => 'senior',
            'education_level' => EducationLevel::LowerSecondary->value,
        ]);
        $this->math = Subject::create(['name' => 'Toán CFG ' . uniqid(), 'status' => ActiveStatus::Active->value]);
        $this->local = Subject::create(['name' => 'GDĐP CFG ' . uniqid(), 'status' => ActiveStatus::Active->value]);
        $this->civic = Subject::create(['name' => 'GDCD CFG ' . uniqid(), 'status' => ActiveStatus::Active->value]);

        DB::table('class_subject')->insert([
            ['class_id' => $this->class->id, 'subject_id' => $this->math->id, 'evaluation_method' => 'score', 'is_required' => 1, 'sort_order' => 1],
            ['class_id' => $this->class->id, 'subject_id' => $this->civic->id, 'evaluation_method' => null, 'is_required' => 1, 'sort_order' => null],
        ]);
    }

    private function update(array $payload): void
    {
        $request = ClassesRequest::create('/admin/classes', 'PUT', array_merge([
            'id' => $this->class->id,
            'name' => $this->class->name,
            'status' => ActiveStatus::Active->value,
        ], $payload));
        $request->setContainer(app())->setRedirector(app('redirect'));
        $request->validateResolved();

        app(ClassesServiceInterface::class)->update($request);
    }

    private function pivot(int $subjectId): ?object
    {
        return DB::table('class_subject')
            ->where('class_id', $this->class->id)->where('subject_id', $subjectId)->first();
    }

    public function test_saves_per_subject_config_for_new_subject(): void
    {
        $this->update([
            'subject_id' => [$this->math->id, $this->civic->id, $this->local->id],
            'subject_config' => [
                $this->local->id => ['evaluation_method' => 'comment', 'is_required' => '1', 'sort_order' => '12'],
            ],
        ]);

        $row = $this->pivot($this->local->id);
        $this->assertSame('comment', $row->evaluation_method);
        $this->assertSame(12, (int) $row->sort_order);
        $this->assertSame(1, (int) $row->is_required);
    }

    public function test_keeps_existing_config_when_not_submitted(): void
    {
        DB::table('class_subject')->where('class_id', $this->class->id)
            ->where('subject_id', $this->math->id)->update(['is_required' => 0, 'sort_order' => 5]);

        $this->update(['subject_id' => [$this->math->id, $this->civic->id]]);

        $math = $this->pivot($this->math->id);
        $this->assertSame('score', $math->evaluation_method);
        $this->assertSame(0, (int) $math->is_required);
        $this->assertSame(5, (int) $math->sort_order);

        // Môn chưa cấu hình được gán mặc định theo cấp học thay vì để NULL
        $this->assertSame('score', $this->pivot($this->civic->id)->evaluation_method);
    }

    public function test_removed_subject_is_detached(): void
    {
        $this->update(['subject_id' => [$this->math->id]]);

        $this->assertNull($this->pivot($this->civic->id));
    }

    public function test_rejects_method_not_allowed_for_education_level(): void
    {
        $this->expectException(ValidationException::class);

        $this->update([
            'subject_id' => [$this->math->id],
            'subject_config' => [$this->math->id => ['evaluation_method' => 'level']],
        ]);
    }

    public function test_does_not_wipe_education_level_when_omitted(): void
    {
        $this->update(['subject_id' => [$this->math->id]]);

        $this->assertSame(EducationLevel::LowerSecondary, $this->class->fresh()->education_level);
    }
}
