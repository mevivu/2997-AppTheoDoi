<?php

namespace App\Api\V1\Services\ChildEvaluation;

use App\Api\V1\Repositories\Capability\CapabilityRepositoryInterface;
use App\Api\V1\Repositories\ChildCapability\ChildCapabilityRepositoryInterface;
use App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepositoryInterface;
use App\Api\V1\Repositories\ChildQuality\ChildQualityRepositoryInterface;
use App\Api\V1\Repositories\ClassGrade\ClassGradeRepositoryInterface;
use App\Api\V1\Repositories\Quality\QualityRepositoryInterface;
use App\Api\V1\Repositories\SubjectGrade\SubjectGradeRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\ActiveStatus;
use Exception;
use Illuminate\Http\Request;


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


    public function __construct(
        ChildEvaluationRepositoryInterface $repository,
        SubjectGradeRepositoryInterface    $subjectGradeRepository,
        ChildQualityRepositoryInterface    $childQualityRepository,
        ChildCapabilityRepositoryInterface $childCapabilityRepository,
        ClassGradeRepositoryInterface      $classGradeRepository,
        QualityRepositoryInterface         $qualityRepository,
        CapabilityRepositoryInterface      $capabilityRepository
    )
    {
        $this->repository = $repository;
        $this->subjectGradeRepository = $subjectGradeRepository;
        $this->childQualityRepository = $childQualityRepository;
        $this->childCapabilityRepository = $childCapabilityRepository;
        $this->classGradeRepository = $classGradeRepository;
        $this->qualityRepository = $qualityRepository;
        $this->capabilityRepository = $capabilityRepository;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $query = $this->classGradeRepository->getByQueryBuilder(
            [
                'child_id' => $data['child_id']
            ],
            ['evaluations']
        );
        return $query->paginate($limit, ['*'], 'page', $page);
    }


    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $subjects = $data['subjects'] ?? [];
        $qualities = $data['qualities'] ?? [];
        $capabilities = $data['capabilities'] ?? [];
        $averageScore = $this->calculateAverageScore($subjects);
        $data['average_score'] = $averageScore;
        $childEvaluation = $this->repository->create($data);
        $childEvaluationId = $childEvaluation->id;
        $this->createSubjectGrade($subjects, $childEvaluationId);
        $this->createChildQuality($qualities, $childEvaluationId);
        $this->createChildCapability($capabilities, $childEvaluationId);

        return $childEvaluation;

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
        $averageScore = $this->calculateAverageScore($subjects);
        $data['average_score'] = $averageScore;
        $childEvaluation = $this->repository->update($childEvaluationId, $data);
        $this->createSubjectGrade($subjects, $childEvaluationId);
        $this->createChildQuality($qualities, $childEvaluationId);
        $this->createChildCapability($capabilities, $childEvaluationId);
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
                    'grade' => $subjectData['grade']
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
    public function findByClassGrade(Request $request): array
    {
        $data = $request->validated();
        $classGrade = $this->classGradeRepository->findOrFail($data['id']);
        $class = $classGrade->class;
        $subjects = $class->subjects;
        $qualities = $this->qualityRepository->getBy(['status' => ActiveStatus::Active]);
        $capabilities = $this->capabilityRepository->getBy(['status' => ActiveStatus::Active]);
        return [
            'class' => $class,
            'subjects' => $subjects,
            'qualities' => $qualities,
            'capabilities' => $capabilities
        ];
    }
}
