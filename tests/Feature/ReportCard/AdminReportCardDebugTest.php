<?php

namespace Tests\Feature\ReportCard;

use App\Enums\Child\BornStatus;
use App\Enums\Child\ChildStatus;
use App\Enums\User\Gender;
use App\Enums\Class\EducationLevel;
use App\Enums\Semester\SemesterStatus;
use App\Enums\ReportCard\EvaluationMethod;
use App\Models\Admin;
use App\Models\Child;
use App\Models\SchoolClass;
use App\Models\ClassGrade;
use App\Models\ClassSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminReportCardDebugTest extends TestCase
{
    use DatabaseTransactions;

    protected Admin $admin;
    protected User $user;
    protected Child $child;
    protected SchoolClass $class;
    protected ClassGrade $classGrade;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Admin with debugReportCard permission
        $this->admin = Admin::create([
            'username' => 'test_admin_' . uniqid(),
            'fullname' => 'Test Admin',
            'email' => 'test_admin_' . uniqid() . '@example.com',
            'phone' => '098' . rand(1000000, 9999999),
            'password' => bcrypt('password123'),
        ]);

        $perm = Permission::firstOrCreate(['name' => 'debugReportCard', 'guard_name' => 'admin']);
        $role = Role::firstOrCreate(['name' => 'superAdmin', 'guard_name' => 'admin']);
        $role->givePermissionTo($perm);
        $this->admin->assignRole($role);

        // 2. Setup User & Child
        $this->user = User::create([
            'username' => 'test_parent_' . uniqid(),
            'code' => 'USR_' . rand(100000, 999999),
            'fullname' => 'Test Parent',
            'phone' => '097' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
        ]);

        $this->child = Child::create([
            'user_id' => $this->user->id,
            'fullname' => 'Bé Test Học Bạ Admin',
            'birthday' => '2012-05-15',
            'gender' => Gender::Male,
            'status' => ChildStatus::Active,
            'is_born' => BornStatus::Born,
        ]);

        // 3. Setup Class 6 (THCS) & Subjects
        $this->class = SchoolClass::firstOrCreate(
            ['id' => 6],
            ['name' => 'Lớp 6', 'education_level' => EducationLevel::LowerSecondary->value]
        );

        $this->classGrade = ClassGrade::firstOrCreate(
            ['child_id' => $this->child->id, 'class_id' => 6],
            ['child_id' => $this->child->id, 'class_id' => 6]
        );

        $toan = Subject::firstOrCreate(['name' => 'Toán Test'], ['name' => 'Toán Test']);
        $van = Subject::firstOrCreate(['name' => 'Ngữ Văn Test'], ['name' => 'Ngữ Văn Test']);

        DB::table('class_subject')->updateOrInsert(
            ['class_id' => 6, 'subject_id' => $toan->id],
            ['evaluation_method' => EvaluationMethod::Score->value, 'is_required' => true]
        );
        DB::table('class_subject')->updateOrInsert(
            ['class_id' => 6, 'subject_id' => $van->id],
            ['evaluation_method' => EvaluationMethod::Score->value, 'is_required' => true]
        );
    }

    public function test_guest_cannot_access_debug_report_card(): void
    {
        $response = $this->postJson(route('admin.children.debugReportCard'), [
            'child_id' => $this->child->id,
            'class_id' => 6,
            'semester' => SemesterStatus::Semester1->value,
        ]);

        // Guest redirected or 401
        $this->assertTrue(in_array($response->getStatusCode(), [302, 401]));
    }

    public function test_admin_can_call_debug_report_card(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.children.debugReportCard'), [
                'child_id' => $this->child->id,
                'class_id' => 6,
                'semester' => SemesterStatus::Semester1->value,
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'child_id',
                'class_id',
                'class_name',
                'education_level',
                'regulation',
                'semester',
                'calculation_status',
                'calculated_academic_performance',
                'subjects',
                'rules',
                'adjustment',
                'missing',
                'warnings',
                'snapshot',
            ],
        ]);
        $this->assertEquals(6, $response->json('data.class_id'));
        $this->assertEquals('lower_secondary', $response->json('data.education_level'));
        $this->assertEquals('TT22/2021', $response->json('data.regulation'));
    }

    public function test_admin_can_recalculate_report_card(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.children.recalculateReportCard'), [
                'child_id' => $this->child->id,
                'class_id' => 6,
                'semester' => SemesterStatus::Semester1->value,
                'apply_final' => 1,
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'evaluation_id',
                'calculation_status',
                'calculated_academic_performance',
                'academic_performance',
            ],
        ]);
    }

    public function test_recalculate_artisan_command_dry_run(): void
    {
        $this->artisan('report-card:recalculate', [
            '--child' => $this->child->id,
            '--class-grade' => 6,
            '--dry-run' => true,
        ])->assertExitCode(0);
    }
}
