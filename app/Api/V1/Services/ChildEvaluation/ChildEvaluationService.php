<?php

namespace App\Api\V1\Services\ChildEvaluation;

use App\Api\V1\Repositories\ChildCapability\ChildCapabilityRepositoryInterface;
use App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepositoryInterface;
use App\Api\V1\Repositories\ChildQuality\ChildQualityRepositoryInterface;
use App\Api\V1\Repositories\SubjectGrade\SubjectGradeRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
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


    public function __construct(
        ChildEvaluationRepositoryInterface $repository,
        SubjectGradeRepositoryInterface    $subjectGradeRepository,
        ChildQualityRepositoryInterface    $childQualityRepository,
        ChildCapabilityRepositoryInterface $childCapabilityRepository
    )
    {
        $this->repository = $repository;
        $this->subjectGradeRepository = $subjectGradeRepository;
        $this->childQualityRepository = $childQualityRepository;
        $this->childCapabilityRepository = $childCapabilityRepository;
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
    public function createSubjectGrade(array $subjects, int $childEvaluationId): void
    {
        foreach ($subjects as $subject) {
            $this->subjectGradeRepository->create(
                [
                    'child_evaluation_id' => $childEvaluationId,
                    'subject_id' => $subject['id'],
                    'grade' => $subject['grade']
                ]
            );
        }
    }

    /**
     * @throws Exception
     */
    public function createChildCapability(array $capabilities, int $childEvaluationId): void
    {
        foreach ($capabilities as $capability) {
            $this->childCapabilityRepository->create([
                'child_evaluation_id' => $childEvaluationId,
                'capability_id' => $capability['id'],
                'capability_status' => $capability['capability_status']
            ]);
        }
    }

    /**
     * @throws Exception
     */
    public function createChildQuality(array $qualities, int $childEvaluationId): void
    {
        foreach ($qualities as $quality) {
            $this->childQualityRepository->create([
                'child_evaluation_id' => $childEvaluationId,
                'quality_id' => $quality['id'],
                'quality_status' => $quality['quality_status']
            ]);
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
}
