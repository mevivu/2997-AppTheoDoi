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
use App\Enums\Semester\SemesterStatus;
use App\Models\ClassGrade;
use Exception;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;


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


    public function __construct(
        ChildEvaluationRepositoryInterface $repository,
        SubjectGradeRepositoryInterface    $subjectGradeRepository,
        ChildQualityRepositoryInterface    $childQualityRepository,
        ChildCapabilityRepositoryInterface $childCapabilityRepository,
        ClassGradeRepositoryInterface      $classGradeRepository,
        QualityRepositoryInterface         $qualityRepository,
        CapabilityRepositoryInterface      $capabilityRepository,
        ClassesRepositoryInterface         $classesRepository
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
        if ($semester == SemesterStatus::Semester1) {
            $classGrade->update([
                'semester1_grade' => $averageScore
            ]);
        } else {
            $semester1Grade = $classGrade->semester1_grade;
            $fullYearGrade = ($semester1Grade + $averageScore * 2) / 3;
            $classGrade->update([
                'semester2_grade' => $averageScore,
                'full_year_grade' => $fullYearGrade
            ]);
        }

    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $childEvaluationId = $data['child_evaluation_id'];
        $subjects = $data['subjects'] ?? [];
        $qualities = $data['qualities'] ?? [];
        $capabilities = $data['capabilities'] ?? [];
        $conduct = $data['conduct'] ?? null;
        $academicPerformance = $data['academic_performance'] ?? null;
        if($conduct == null){
            unset($data['conduct']);
        }
        if($academicPerformance == null){
            unset($data['academic_performance']);
        }
        if (isEmpty($subjects)) {
            $averageScore =  $this->calculateAverageScore($subjects);
            $data['average_score'] = $averageScore;
        }
        $childEvaluation = $this->repository->update($childEvaluationId, $data);
        $classGrade = $childEvaluation->classGrade;
        if (isEmpty($subjects)) {
            $this->createSubjectGrade($subjects, $childEvaluationId);
        }
        if (isEmpty($qualities)) {
            $this->createChildQuality($qualities, $childEvaluationId);
        }
        if (isEmpty($capabilities)) {
            $this->createChildCapability($capabilities, $childEvaluationId);
        }
        $semester = $childEvaluation->semester;
        $this->updateScoreClassGrade($semester, $classGrade, $averageScore);

        return $childEvaluation;

    }

    /**
     * @throws Exception
     */
    public function createSubjectGrade(array $subjects, int $childEvaluationId): void
    {
        foreach ($subjects as $subjectData) {
            $this->subjectGradeRepository->updateOrCreate(
                [
                    'child_evaluation_id' => $childEvaluationId,
                    'subject_id' => $subjectData['id'],
                ],
                [
                    'grade' => $subjectData['grade'],
                    'remark' => $subjectData['remark'] ?? null,
                    'achievement_level' => $subjectData['achievement_level'] ?? null,
                ]
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
                    'capability_status' => $capabilityData['capability_status']
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
                    'quality_status' => $quality['quality_status']
                ]
            );
        }
    }

    private function calculateAverageScore(array $subjects): float
    {
        $totalScore = 0;
        foreach ($subjects as $subject) {
            $totalScore += $subject['grade'];
        }
        return count($subjects) > 0 ? $totalScore / count($subjects) : 0;
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
        $classes = $this->classesRepository->getBy(['status' => ActiveStatus::Active]);
        $subjects = $class->subjects;
        $capabilities = $this->capabilityRepository->getBy(['status' => ActiveStatus::Active]);
        $qualities = $this->qualityRepository->getBy(['status' => ActiveStatus::Active]);
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
            ]

        ];
    }

    public function findAndCreateChildEvaluations($childId, $classId, $semester)
    {
        $classGrade = $this->classGradeRepository->getByQueryBuilder(
            [
                'child_id' => $childId,
                'class_id' => $classId
            ]
        )->first();
        $evaluation = $classGrade->evaluations()->where('semester', $semester)->first();

        if (!$evaluation) {
            $evaluation = $classGrade->evaluations()->create([
                'semester' => $semester,
                'status' => ActiveStatus::Draft,
                'conduct' => ConductRating::Pending,
                'average_score' => 0,
                'academic_performance' => AcademicRating::Pending
            ]);

        }
        return $evaluation;
    }

}
