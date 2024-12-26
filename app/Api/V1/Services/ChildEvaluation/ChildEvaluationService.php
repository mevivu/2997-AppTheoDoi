<?php

namespace App\Api\V1\Services\ChildEvaluation;

use App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepositoryInterface;
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


    public function __construct(
        ChildEvaluationRepositoryInterface $repository,
        SubjectGradeRepositoryInterface    $subjectGradeRepository
    )
    {
        $this->repository = $repository;
        $this->subjectGradeRepository = $subjectGradeRepository;
    }


    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $subjects = $data['subjects'] ?? [];
        $averageScore = $this->calculateAverageScore($subjects);
        $data['average_score'] = $averageScore;
        $childEvaluation = $this->repository->create($data);
        foreach ($subjects as $subject) {
            $this->subjectGradeRepository->create(
                [
                    'child_evaluation_id' => $childEvaluation->id,
                    'subject_id' => $subject['id'],
                    'grade' => $subject['grade']
                ]
            );
        }

        return $childEvaluation;

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
