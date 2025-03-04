<?php

namespace App\Api\V1\Services\Assessment;

use App\Api\V1\Repositories\Assessment\AssessmentRepositoryInterface;

use App\Api\V1\Repositories\Rating\RatingRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Assessment\AssessmentType;
use App\Enums\OpenStatus;
use App\Enums\Question\QuestionType;
use Illuminate\Http\Request;


class AssessmentService implements AssessmentServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected AssessmentRepositoryInterface $repository;
    protected RatingPQRepositoryInterface $ratingPQRepository;
    protected RatingRepositoryInterface $ratingRepository;


    public function __construct(
        AssessmentRepositoryInterface $repository,
        RatingPQRepositoryInterface   $ratingPQRepository,
        RatingRepositoryInterface     $ratingRepository,
    )
    {
        $this->repository = $repository;
        $this->ratingPQRepository = $ratingPQRepository;
        $this->ratingRepository = $ratingRepository;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $childId = $data['child_id'];
        $assessmentPQ = $this->repository->getBy([
            'child_id' => $childId,
            'type' => AssessmentType::PQ
        ])->first();
        $assessmentIq = $this->repository->getBy(
            [
                'child_id' => $childId,
                'type' => AssessmentType::IQ
            ]
        )->first();
        $assessmentEQ = $this->repository->getBy(
            [
                'child_id' => $childId,
                'type' => AssessmentType::EQ
            ]
        )->first();
        $assessmentAQ = $this->repository->getBy(
            [
                'child_id' => $childId,
                'type' => AssessmentType::AQ
            ]
        )->first();
        $this->updateAssessmentPQ($assessmentPQ, $childId);
        $this->updateAssessmentType($assessmentIq, $childId, QuestionType::IQ);
        $this->updateAssessmentType($assessmentEQ, $childId, QuestionType::EQ);
        $this->updateAssessmentType($assessmentAQ, $childId, QuestionType::AQ);
        return $this->repository->getBy([
            'child_id' => $childId,
        ]);
    }

    public function updateAssessmentPQ($assessmentPQ, $childId): void
    {
        $ratingPQExists = $this->ratingPQRepository->exists(['child_id' => $childId]);
        if ($ratingPQExists) {
            $assessmentPQ->update(['checked' => OpenStatus::ON]);
        }
    }

    public function updateAssessmentType($assessment, $childId, $type): void
    {
        $ratingPQExists = $this->ratingRepository->exists(
            [
                'child_id' => $childId,
                'type' => $type
            ]
        );
        if ($ratingPQExists) {
            $assessment->update(['checked' => OpenStatus::ON]);
        }
    }
}
