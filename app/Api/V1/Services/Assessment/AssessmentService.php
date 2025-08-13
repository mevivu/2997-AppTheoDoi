<?php

namespace App\Api\V1\Services\Assessment;

use App\Api\V1\Repositories\Assessment\AssessmentRepositoryInterface;

use App\Api\V1\Repositories\ClassGrade\ClassGradeRepositoryInterface;
use App\Api\V1\Repositories\Rating\RatingRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Assessment\AssessmentType;
use App\Enums\OpenStatus;
use App\Enums\Question\QuestionType;
use App\Enums\VerifiedStatus;
use App\Models\Assessment;
use App\Models\Rating;
use App\Models\RatingPQ;
use Illuminate\Http\Request;
use Throwable;


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
    protected ClassGradeRepositoryInterface $classGradeRepository;


    public function __construct(
        AssessmentRepositoryInterface $repository,
        RatingPQRepositoryInterface   $ratingPQRepository,
        RatingRepositoryInterface     $ratingRepository,
        ClassGradeRepositoryInterface $classGradeRepository
    )
    {
        $this->repository = $repository;
        $this->ratingPQRepository = $ratingPQRepository;
        $this->ratingRepository = $ratingRepository;
        $this->classGradeRepository = $classGradeRepository;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $childId = $data['child_id'];
        $assessmentPQ = $this->getAssessmentByType($childId, AssessmentType::PQ);
        $assessmentIq = $this->getAssessmentByType($childId, AssessmentType::IQ);
        $assessmentEQ = $this->getAssessmentByType($childId, AssessmentType::EQ);
        $assessmentAQ = $this->getAssessmentByType($childId, AssessmentType::AQ);
        $assessmentGPA = $this->getAssessmentByType($childId, AssessmentType::GPA);

        $this->updateAssessmentPQ($assessmentPQ, $childId);
        $this->updateAssessmentGPA($assessmentGPA, $childId);
        $this->updateAssessmentType($assessmentIq, $childId, QuestionType::IQ);
        $this->updateAssessmentType($assessmentEQ, $childId, QuestionType::EQ);
        $this->updateAssessmentType($assessmentAQ, $childId, QuestionType::AQ);
        $latestIq = $this->getLatestRatingByTypeIQ($childId, QuestionType::IQ);
        $latestEq = $this->getLatestRatingByType($childId, QuestionType::EQ);
        $latestAq = $this->getLatestRatingByType($childId, QuestionType::AQ);
        $latestGpa = $this->getLatestGpaScore($childId);
        $assessment = $this->repository->getBy([
            'child_id' => $childId,
        ]);
        return [
            'assessments' => $assessment,
            'information' => [
                'iq' => $latestIq?->score,
                'eq' => $latestEq?->score,
                'aq' => $latestAq?->score,
                'gpa' => $latestGpa,
                'pq' => $this->getLatestPQScore($childId),
            ]
        ];
    }

    public function getLatestPQScore(int $childId): ?float
    {
        $latest = RatingPQ::where('child_id', $childId)
            ->orderByDesc('assessment_date')
            ->first();

        if (!$latest) {
            return null;
        }

        $age = $latest->child?->age ?? null;
        if (is_null($age)) return null;

        try {
            $currentHeight = floatval($latest->height_result);
            $heightAdulthood = floatval($latest->height_change);
            $bmiPercent = floatval($latest->bmi);
            $strengthPercent = floatval($latest->strength);
            $endurancePercent = floatval($latest->endurance);

            if (
                is_null($currentHeight) || is_null($heightAdulthood) ||
                is_null($strengthPercent) || is_null($endurancePercent)
            ) {
                return null;
            }

            if ($age > 5) {
                if (is_null($bmiPercent)) return null;
                $totalScore = $bmiPercent + $endurancePercent + $strengthPercent + $currentHeight + $heightAdulthood;
                return min(round($totalScore / 5, 1), 10);
            } else {
                $totalScore = $endurancePercent + $strengthPercent + $currentHeight + $heightAdulthood;
                return min(round($totalScore / 4, 1), 10);
            }
        } catch (Throwable $e) {
            // log lỗi nếu cần
            return null;
        }
    }


    public function getLatestRatingByTypeIQ(int $childId, QuestionType $type): ?Rating
    {
        return Rating::where('child_id', $childId)
            ->where('type', $type)
            ->whereNotNull('score')
            ->orderByDesc('age')   // Tuổi cao nhất
            ->orderByDesc('created_at') // Nếu có nhiều bản ghi cùng tuổi, lấy bản mới nhất
            ->first();
    }

    public function getLatestRatingByType(int $childId, QuestionType $type): ?Rating
    {
        return Rating::where('child_id', $childId)
            ->where('type', $type)
            ->orderByDesc('created_at')
            ->first();
    }

    public function getLatestGpaScore(int $childId): ?float
    {
        $latestGrade = $this->classGradeRepository->getQueryBuilder()
            ->where('child_id', $childId)
            ->whereNotNull('full_year_grade')
            ->orderByDesc('class_id')
            ->first();

        return $latestGrade?->full_year_grade;
    }


    public function getAssessmentByType(int $childId, AssessmentType $type): ?Assessment
    {
        return $this->repository->getBy([
            'child_id' => $childId,
            'type' => $type
        ])->first();
    }

    public function updateAssessmentPQ($assessmentPQ, $childId): void
    {
        $ratingPQExists = $this->ratingPQRepository->exists(['child_id' => $childId]);
        if ($ratingPQExists) {
            $assessmentPQ->update(['checked' => OpenStatus::ON]);
        }
    }

    public function updateAssessmentGPA($assessmentGPA, $childId): void
    {
        $query = $this->classGradeRepository->getQueryBuilder();
        $query->where('child_id', $childId)
            ->with('evaluations')
            ->join('classes', 'class_grades.class_id', '=', 'classes.id')
            ->orderBy('classes.id', 'asc')
            ->select('class_grades.*');

        $details = $query->first();

        if ($details && $details->evaluations->isNotEmpty()) {
            $assessmentGPA->update(['checked' => OpenStatus::ON]);
        } else {
            $assessmentGPA->update(['checked' => OpenStatus::OFF]);
        }
    }


    public function updateAssessmentType($assessment, $childId, $type): void
    {
        if ($type == QuestionType::IQ) {
            $ratingPQExists = $this->ratingRepository->exists(
                [
                    'child_id' => $childId,
                    'type' => $type,
                    'status' => VerifiedStatus::Active
                ]
            );
        } else {
            $ratingPQExists = $this->ratingRepository->exists(
                [
                    'child_id' => $childId,
                    'type' => $type,
                ]
            );
        }
        if ($ratingPQExists) {
            $assessment->update(['checked' => OpenStatus::ON]);
        }
    }
}
