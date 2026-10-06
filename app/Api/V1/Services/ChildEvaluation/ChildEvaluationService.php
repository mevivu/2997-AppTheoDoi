<?php

namespace App\Api\V1\Services\ChildEvaluation;

use App\Api\V1\Repositories\Capability\CapabilityRepositoryInterface;
use App\Api\V1\Repositories\ChildCapability\ChildCapabilityRepositoryInterface;
use App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepositoryInterface;
use App\Api\V1\Repositories\ChildQuality\ChildQualityRepositoryInterface;
use App\Api\V1\Repositories\Classes\ClassesRepositoryInterface;
use App\Api\V1\Repositories\ClassGrade\ClassGradeRepositoryInterface;
use App\Api\V1\Repositories\Quality\QualityRepositoryInterface;
use App\Api\V1\Repositories\SubjectGrade\SubjectGradeRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\ActiveStatus;
use App\Enums\ChildEvaluation\AcademicRating;
use App\Enums\ChildEvaluation\ConductRating;
use App\Enums\Class\EducationLevel;
use App\Enums\Class\LevelGroup;
use App\Enums\ReportCard\FullYearGradeSource;
use App\Enums\Semester\SemesterStatus;
use App\Models\ChildEvaluation;
use App\Models\ClassGrade;
use App\Services\ReportCard\ReportCardService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;


class ChildEvaluationService implements ChildEvaluationServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ChildEvaluationRepositoryInterface $repository;
    protected SubjectGradeRepositoryInterface $subjectGradeRepository;
    protected ChildQualityRepositoryInterface $childQualityRepository;
    protected ChildCapabilityRepositoryInterface $childCapabilityRepository;
    protected ClassGradeRepositoryInterface $classGradeRepository;
    protected QualityRepositoryInterface $qualityRepository;
    protected CapabilityRepositoryInterface $capabilityRepository;
    protected ClassesRepositoryInterface $classesRepository;
    protected ReportCardService $reportCardService;

    public function __construct(
        ChildEvaluationRepositoryInterface $repository,
        SubjectGradeRepositoryInterface    $subjectGradeRepository,
        ChildQualityRepositoryInterface    $childQualityRepository,
        ChildCapabilityRepositoryInterface $childCapabilityRepository,
        ClassGradeRepositoryInterface      $classGradeRepository,
        QualityRepositoryInterface         $qualityRepository,
        CapabilityRepositoryInterface      $capabilityRepository,
        ClassesRepositoryInterface         $classesRepository,
        ?ReportCardService                 $reportCardService = null
    )
    {
        $this->repository = $repository;
        $this->subjectGradeRepository = $subjectGradeRepository;
        $this->childQualityRepository = $childQualityRepository;
        $this->childCapabilityRepository = $childCapabilityRepository;
        $this->classGradeRepository = $classGradeRepository;
        $this->qualityRepository = $qualityRepository;
        $this->capabilityRepository = $capabilityRepository;
        $this->classesRepository = $classesRepository;
        $this->reportCardService = $reportCardService ?? app(ReportCardService::class);
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;

        $query = $this->classGradeRepository->getQueryBuilder();
        $query->where('child_id', $data['child_id'])
            ->with('evaluations')
            ->join('classes', 'class_grades.class_id', '=', 'classes.id')
            ->orderBy('classes.id', 'asc')
            ->select('class_grades.*');

        return $query->paginate($limit, ['*'], 'page', $page);
    }


    /**
     * @throws Exception
     */


    private function updateScoreClassGrade($semester, ClassGrade $classGrade, $averageScore): void
    {
        $class = $classGrade->class;
        $educationLevel = $class?->resolvedEducationLevel() ?? EducationLevel::fromClassId($classGrade->class_id);
        $isPrimary = ($educationLevel === EducationLevel::Primary);

        if ($semester == SemesterStatus::Semester1) {
            $classGrade->semester1_grade = $averageScore;

            // Nếu là Lớp 6-12 và đã có điểm HK2 thì tính điểm cả năm
            if (!$isPrimary && !is_null($classGrade->semester2_grade)) {
                $classGrade->full_year_grade = round(($averageScore + 2 * $classGrade->semester2_grade) / 3, 2);
            }

            $classGrade->save();
        } else {
            $classGrade->semester2_grade = $averageScore;

            if ($isPrimary) {
                // Lớp 1-5: Cả năm = Điểm HK2
                $classGrade->full_year_grade = $averageScore;
            } else {
                // Lớp 6-12: Cả năm = (HK1 + 2 * HK2) / 3
                if (!is_null($classGrade->semester1_grade)) {
                    $classGrade->full_year_grade = round(($classGrade->semester1_grade + 2 * $averageScore) / 3, 2);
                } else {
                    $classGrade->full_year_grade = $averageScore;
                }
            }

            $classGrade->save();
        }
    }


    /**
     * @throws Exception|Throwable
     */
    public function update(Request $request): object
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $childEvaluationId = $data['child_evaluation_id'];
            $subjects = $data['subjects'] ?? [];
            $qualities = $data['qualities'] ?? [];
            $capabilities = $data['capabilities'] ?? [];
            $conduct = $data['conduct'] ?? null;
            $academicPerformance = $data['academic_performance'] ?? null;
            if ($conduct == null) {
                unset($data['conduct']);
            }
            if ($academicPerformance == null) {
                unset($data['academic_performance']);
            }

            if (!empty($subjects)) {
                $averageScore = $this->calculateAverageScore($subjects);
                $data['average_score'] = $averageScore;
            }

            // Xử lý cờ ghi đè học lực nếu client gửi lên
            if (array_key_exists('override_academic_performance', $data)) {
                $data['is_performance_overridden'] = (bool) $data['override_academic_performance'];
                unset($data['override_academic_performance']);
            }

            $childEvaluation = $this->repository->update($childEvaluationId, $data);
            $classGrade = ClassGrade::whereKey($childEvaluation->class_grade_id)->lockForUpdate()->first();
            $class = $classGrade?->class;
            $educationLevel = $class?->resolvedEducationLevel() ?? EducationLevel::fromClassId($classGrade->class_id);
            $isPrimary = ($educationLevel === EducationLevel::Primary);

            $semester = $childEvaluation->semester;

            if (!empty($subjects)) {
                $this->createSubjectGrade($subjects, $childEvaluationId);
            }
            if ($isPrimary && !empty($qualities)) {
                $this->createChildQuality($qualities, $childEvaluationId);
            }
            if ($isPrimary && !empty($capabilities)) {
                $this->createChildCapability($capabilities, $childEvaluationId);
            }

            if ($childEvaluation->semester == SemesterStatus::FullYear) {
                $this->autoSyncFullYearEvaluation($classGrade, $childEvaluation);
                $fullYearGrade = $this->calculateFullYearGradeFromSubjects($subjects);
                if ($fullYearGrade !== null) {
                    $classGrade?->update(['full_year_grade' => $fullYearGrade]);
                }
            } else {
                if ($classGrade) {
                    $this->updateScoreClassGrade($semester, $classGrade, $childEvaluation->average_score);
                    $this->autoSyncFullYearEvaluation($classGrade);
                }
            }

            // Tự động tính toán lại học bạ bằng engine nếu cờ tính năng được bật
            if ($classGrade && config('report_card.engine_enabled', false)) {
                $this->reportCardService->recalculateClassGrade($classGrade, $childEvaluation->id);
            }

            return $childEvaluation->fresh(['subjectGrades', 'qualities', 'capabilities']);
        });
    }

    /**
     * Tự động tính toán và đồng bộ điểm cả năm của các môn học và điểm tổng kết cả năm:
     * - Lớp 1-5 (Tiểu học): Điểm tbm cả năm = Điểm HK2 (theo TT27).
     * - Lớp 6-12 (THCS & THPT): Điểm tbm cả năm = round((HK1 + HK2 * 2) / 3, 1) (theo TT22).
     */
    public function autoSyncFullYearEvaluation(ClassGrade $classGrade, ?ChildEvaluation $fullYearEvaluation = null): ?ChildEvaluation
    {
        $class = $classGrade->class;
        $educationLevel = $class?->resolvedEducationLevel() ?? EducationLevel::fromClassId($classGrade->class_id);
        $isPrimary = ($educationLevel === EducationLevel::Primary);

        $allEvaluations = $classGrade->evaluations()->with('subjectGrades')->get();
        $sem1 = $allEvaluations->firstWhere('semester', SemesterStatus::Semester1);
        $sem2 = $allEvaluations->firstWhere('semester', SemesterStatus::Semester2);

        if (!$fullYearEvaluation) {
            $fullYearEvaluation = $allEvaluations->firstWhere('semester', SemesterStatus::FullYear);
        }

        if (!$fullYearEvaluation) {
            return null;
        }

        $sem1SubjectGrades = $sem1 ? $sem1->subjectGrades->keyBy('subject_id') : collect();
        $sem2SubjectGrades = $sem2 ? $sem2->subjectGrades->keyBy('subject_id') : collect();
        $existingFullYearGrades = $fullYearEvaluation->subjectGrades->keyBy('subject_id');

        $subjectIds = $sem1SubjectGrades->keys()
            ->merge($sem2SubjectGrades->keys())
            ->merge($existingFullYearGrades->keys())
            ->unique();

        $allSubjectFullYearScores = [];

        foreach ($subjectIds as $subjectId) {
            $existing = $existingFullYearGrades->get($subjectId);

            $sourceVal = $existing?->full_year_grade_source instanceof \BackedEnum
                ? $existing->full_year_grade_source->value
                : $existing?->full_year_grade_source;

            // Nếu người dùng đã tự nhập tay hoặc chủ động ghi đè, tôn trọng lựa chọn của người dùng
            if ($sourceVal === FullYearGradeSource::Overridden->value || $sourceVal === FullYearGradeSource::Manual->value) {
                if ($existing && $existing->full_year_grade !== null) {
                    $allSubjectFullYearScores[] = (float) $existing->full_year_grade;
                }
                continue;
            }

            $s1Grade = $sem1SubjectGrades->get($subjectId)?->grade;
            $s2Grade = $sem2SubjectGrades->get($subjectId)?->grade;

            $computedGrade = null;

            if ($isPrimary) {
                // Lớp 1-5: Điểm tbm cả năm = điểm hk2
                if ($s2Grade !== null && $s2Grade !== '') {
                    $computedGrade = (float) $s2Grade;
                }
            } else {
                // Lớp 6-12: Điểm tbm Cả năm = (hk1 + hk2*2)/3, làm tròn 1 chữ số thập phân theo TT22
                if ($s1Grade !== null && $s1Grade !== '' && $s2Grade !== null && $s2Grade !== '') {
                    $computedGrade = round(((float) $s1Grade + 2 * (float) $s2Grade) / 3, 1);
                }
            }

            $payload = [];
            if ($computedGrade !== null) {
                $payload['full_year_grade'] = $computedGrade;
                $payload['full_year_grade_source'] = FullYearGradeSource::Auto;
                $allSubjectFullYearScores[] = $computedGrade;
            }

            // Với tiểu học, nếu môn đánh giá mức đạt được (T, H, C), lấy kết quả HK2 làm cả năm
            if ($isPrimary && empty($existing?->achievement_level)) {
                $s2Level = $sem2SubjectGrades->get($subjectId)?->achievement_level;
                if ($s2Level) {
                    $payload['achievement_level'] = $s2Level;
                }
            }

            if (!empty($payload)) {
                $this->subjectGradeRepository->updateOrCreate(
                    [
                        'child_evaluation_id' => $fullYearEvaluation->id,
                        'subject_id' => $subjectId,
                    ],
                    $payload
                );
            } elseif ($existing && $existing->full_year_grade !== null) {
                $allSubjectFullYearScores[] = (float) $existing->full_year_grade;
            }
        }

        // Cập nhật điểm trung bình của Cả năm và class_grade->full_year_grade
        if (!empty($allSubjectFullYearScores)) {
            $avgScore = round(array_sum($allSubjectFullYearScores) / count($allSubjectFullYearScores), 2);
            $fullYearEvaluation->update(['average_score' => $avgScore]);
            $classGrade->update(['full_year_grade' => $avgScore]);
        }

        return $fullYearEvaluation;
    }

    /**
     * Tính điểm cả năm từ các full_year_grade của các môn học
     */
    private function calculateFullYearGradeFromSubjects($subjects): ?float
    {
        $totalScore = 0;
        $count = 0;

        foreach ($subjects as $subject) {
            if (isset($subject['full_year_grade']) && !is_null($subject['full_year_grade']) && is_numeric($subject['full_year_grade'])) {
                $totalScore += (float) $subject['full_year_grade'];
                $count++;
            }
        }

        return $count > 0 ? round($totalScore / $count, 2) : null;
    }

    /**
     * @throws Exception
     */
    public function createSubjectGrade(array $subjects, int $childEvaluationId): void
    {
        foreach ($subjects as $subjectData) {
            $payload = [
                'remark' => $subjectData['remark'] ?? null,
                'achievement_level' => $subjectData['achievement_level'] ?? null,
            ];

            if (array_key_exists('grade', $subjectData)) {
                $payload['grade'] = $subjectData['grade'];
            }

            if (!empty($subjectData['override_full_year_grade'])) {
                $payload['full_year_grade'] = $subjectData['full_year_grade'] ?? null;
                $payload['full_year_grade_source'] = FullYearGradeSource::Overridden;
            } elseif (array_key_exists('full_year_grade', $subjectData) && $subjectData['full_year_grade'] !== null && $subjectData['full_year_grade'] !== '') {
                $payload['full_year_grade'] = $subjectData['full_year_grade'];
                $payload['full_year_grade_source'] = FullYearGradeSource::Manual;
            }

            $this->subjectGradeRepository->updateOrCreate(
                [
                    'child_evaluation_id' => $childEvaluationId,
                    'subject_id' => $subjectData['id'],
                ],
                $payload
            );
        }
    }

    /**
     * @throws Exception
     */
    public function createChildCapability(array $capabilities, int $childEvaluationId): void
    {
        foreach ($capabilities as $capabilityData) {
            $this->childCapabilityRepository->updateOrCreate(
                [
                    'child_evaluation_id' => $childEvaluationId,
                    'capability_id' => $capabilityData['id'],
                ],
                [
                    'capability_status' => $capabilityData['capability_status'] ?? null,
                    'remark' => $capabilityData['remark'] ?? null,
                ]
            );
        }
    }

    /**
     * @throws Exception
     */
    public function createChildQuality(array $qualities, int $childEvaluationId): void
    {
        foreach ($qualities as $quality) {
            $this->childQualityRepository->updateOrCreate(
                [
                    'child_evaluation_id' => $childEvaluationId,
                    'quality_id' => $quality['id'],
                ],
                [
                    'quality_status' => $quality['quality_status'] ?? null,
                    'remark' => $quality['remark'] ?? null,
                ]
            );
        }
    }

    private function calculateAverageScore(array $subjects): float|int|null
    {
        $totalScore = 0;
        $count = 0;

        foreach ($subjects as $subject) {
            if (isset($subject['grade'])) {
                $totalScore += $subject['grade'];
                $count++;
            }
        }

        return $count > 0 ? $totalScore / $count : null;
    }


    /**
     * @throws Exception
     */
    public function show($id)
    {
        return $this->repository->findOrFail($id);
    }

    public function search(Request $request)
    {
        $data = $request->validated();
        $classId = $data['class_id'];
        $semester = $data['semester'];
        $classGradeId = $data['class_grade_id'];
        $query = $this->repository->getBy([
            ['classGrade.class_id', '=', $classId],
            'semester' => $semester,
            'class_grade_id' => $classGradeId,
        ]);
        return $query->first();


    }


    /**
     * @throws Exception
     */
    public function findByClass(Request $request): array
    {
        $data = $request->validated();
        $classId = $data['class_id'];
        $semester = $data['semester'];
        $childId = $data['child_id'];
        $class = $this->classesRepository->findOrFail($classId);
        $childEvaluation = $this->findAndCreateChildEvaluations($childId, $classId, $semester);

        $semVal = $semester instanceof \BackedEnum ? $semester->value : $semester;
        if ($semVal === SemesterStatus::FullYear->value) {
            $classGrade = $childEvaluation->classGrade ?? ClassGrade::whereKey($childEvaluation->class_grade_id)->first();
            if ($classGrade) {
                $this->autoSyncFullYearEvaluation($classGrade, $childEvaluation);
            }
        }

        $educationLevel = $class->resolvedEducationLevel();
        $isPrimary = ($educationLevel === EducationLevel::Primary);

        $relations = ['subjectGrades.subject', 'attachments'];
        if ($isPrimary) {
            $relations[] = 'qualities';
            $relations[] = 'capabilities';
        }
        $childEvaluation->load($relations);

        $classes = $this->classesRepository->getBy(['status' => ActiveStatus::Active]);
        $subjects = $class->subjects()->withPivot('evaluation_method', 'is_required', 'sort_order')->orderByPivot('sort_order')->get();
        $capabilities = $isPrimary ? $this->capabilityRepository->getBy(['status' => ActiveStatus::Active]) : [];
        $qualities = $isPrimary ? $this->qualityRepository->getBy(['status' => ActiveStatus::Active]) : [];
        return [
            'detail' => [
                'child_evaluation' => $childEvaluation,
            ],
            'systems' => [
                'class' => $classes,
                'subjects' => $subjects,
                'capabilities' => $capabilities,
                'qualities' => $qualities,
                'semester' => SemesterStatus::asSelectArray(),
                'education_level' => $educationLevel->value,
            ]
        ];
    }

    public function findAndCreateChildEvaluations($childId, $classId, $semester)
    {
        return DB::transaction(function () use ($childId, $classId, $semester) {
            $classGrade = $this->classGradeRepository->getByQueryBuilder(
                [
                    'child_id' => $childId,
                    'class_id' => $classId
                ]
            )->first();

            // Bé tạo trước khi có lớp mới (hoặc dữ liệu cũ thiếu) sẽ không có class_grade → trước đây gây lỗi 500.
            if (!$classGrade) {
                $classGrade = ClassGrade::create([
                    'child_id' => $childId,
                    'class_id' => $classId,
                    'semester1_grade' => null,
                    'semester2_grade' => null,
                    'full_year_grade' => null,
                    'status' => ActiveStatus::Draft->value,
                ]);
            }

            // Khóa dòng class_grade theo khóa chính (record lock, không phải gap lock) để các request
            // song song cùng bé/lớp/kỳ xếp hàng, tránh tạo trùng child_evaluation.
            $classGrade = ClassGrade::whereKey($classGrade->id)->lockForUpdate()->first();

            $evaluation = $classGrade->evaluations()->where('semester', $semester)->first();

            if (!$evaluation) {
                $evaluation = $classGrade->evaluations()->create([
                    'semester' => $semester,
                    'status' => ActiveStatus::Draft,
                    'conduct' => ConductRating::Pending,
                    'average_score' => null,
                    'academic_performance' => AcademicRating::Pending
                ]);
            }

            return $evaluation;
        });
    }

}
