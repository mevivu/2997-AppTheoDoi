<?php

namespace App\Api\V1\Services\RatingPQ;


use App\Admin\Repositories\Bmi\BmiRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Api\V1\Http\Resources\RatingPQ\RatingPQMonthResource;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Repositories\WeightHeightWho\WhoRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class RatingPQService implements RatingPQServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected RatingPQRepositoryInterface $repository;
    protected ChildRepositoryInterface $childRepository;
    protected BmiRepositoryInterface $bmiRepository;
    protected WhoRepositoryInterface $whoRepository;
    protected FileService $fileService;

    public function __construct(
        RatingPQRepositoryInterface $repository,
        ChildRepositoryInterface    $childRepository,
        BmiRepositoryInterface      $bmiRepository,
        WhoRepositoryInterface      $whoRepository,
        FileService                 $fileService
    )
    {
        $this->repository = $repository;
        $this->childRepository = $childRepository;
        $this->bmiRepository = $bmiRepository;
        $this->whoRepository = $whoRepository;
        $this->fileService = $fileService;
    }

    public function getMonthlyEnduranceData(Request $request): array
    {
        $validated = $request->validated();

        $childId = $validated['child_id'];
        $month = (int)$validated['month'];
        $year = (int)$validated['year'];

        $records = $this->repository->getQueryBuilder()
            ->where('child_id', $childId)
            ->whereMonth('assessment_date', '=', $month)
            ->whereYear('assessment_date', '=', $year)
            ->orderBy('assessment_date', 'desc')
            ->get();

        $result = [];
        $lastDate = null;
        foreach ($records as $record) {
            $date = $record->assessment_date->toDateString();
            if ($date !== $lastDate) {
                $result[] = new RatingPQMonthResource($record);
                $lastDate = $date;
            }
        }

        return $result;

    }


    public function index(Request $request)
    {
        $data = $request->validated();

        $limit = $data['limit'] ?? null;
        $page = $data['page'] ?? 1;

        $query = $this->repository->getByQueryBuilder([
            'child_id' => $data['child_id'],
        ]);
        $totalCount = $query->count();
        $newLimit = $limit != null ? $limit : $totalCount;

        return $query->paginate($newLimit, ['*'], 'page', $page);
    }


    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $height = $data['height'];
        $weight = $data['weight'];
        $currentEndurance = $data['endurance'];
        $currentStrength = $data['strength'];
        $child = $this->childRepository->findOrFail($data['child_id']);
        // bmi hien tai
        $bmi = $this->calculateBMI($height, $weight);
        $age = $child->age;
        $gender = $child->gender;
        $month = $child->month;
        $who = $this->getWho($month, $gender);
        $whoHeight = $who->height;
        $bmiCategory = $this->getBmiCategory($bmi, $age, $gender);
        $data['bmi'] = $bmi;
        $data['bmi_result'] = $bmiCategory;
        $data['height_change'] = $height - $whoHeight;
        $data['height_result'] = $this->getHeightResult($height, $who);
        $data['score'] = $this->calculateScore($bmi, $age, $gender,
            $child->id, $currentEndurance, $currentStrength, $height);

        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function calculateScore($currentBmi, $age, $gender, $childId, $currentEndurance, $currentStrength, $currenHeight): float
    {
        $bmi = $this->getBmi($age, $gender);
        $child = $this->childRepository->findOrFail($childId);
        $bmiPercent = $this->getBmiPercent($bmi, $currentBmi);
        $endurancePercent = $this->getEndurance($childId, $currentEndurance);
        $strengthPercent = $this->getStrength($childId, $currentStrength);
        $currentHeight = $this->getCurrentHeight($child, $gender);
        $heightAdulthood = $this->getHeightAdulthood($child, $currenHeight, $gender);

        if ($age > 5) {
            $totalScore = $bmiPercent + $endurancePercent + $strengthPercent + $currentHeight + $heightAdulthood;
            return round($totalScore / 5, 1);
        } else {
            $totalScore = $endurancePercent + $strengthPercent + $currentHeight + $heightAdulthood;
            return round($totalScore / 4);
        }

    }

    public function getCurrentHeight($child, $gender)
    {
        $currentDate = Carbon::now()->startOfDay();
        $oneYearAgo = $currentDate->copy()->subYear()->startOfDay();
        $nearestRatingPQ = $this->findRatingPQ($child->id, $currentDate, $oneYearAgo);
        $nearestHeight = ($nearestRatingPQ && isset($nearestRatingPQ->height)) ? $nearestRatingPQ->height : 0;
        $who228 = $this->getWho(228, $gender);
        $heightWho = $who228->height;
        $result = ($nearestHeight / $heightWho) / 0.1;
        return min($result, 10);
    }

    /**
     * param float| int currenHeight người dùng nhập
     */
    public function getHeightAdulthood($child, $currenHeight, $gender)
    {

        $currentDate = Carbon::now()->startOfDay();
        $oneYearAgo = $currentDate->copy()->subYear()->startOfDay();
        $childBirthDate = $child->birthday;
        $nearestRatingPQ = $this->findRatingPQ($child->id, $currentDate, $oneYearAgo);
        $nearestHeight = $nearestRatingPQ->height ?? 0;
        $nearestAssessmentDate = $nearestRatingPQ ? $nearestRatingPQ->assessment_date : $currentDate;

        $heightIncreaseInOneYear = abs($currenHeight - $nearestHeight);
        $diffInDaysCurrent = $currentDate->diffInDays($nearestAssessmentDate);
        $diffInDaysBirth = $childBirthDate->diffInDays($nearestAssessmentDate);
        $monthCompare = $diffInDaysCurrent / 30.5;
        $currentAge = $diffInDaysBirth / 365.3;
        if ($gender == Gender::Male) {
            $yearsToAdulthood = 16 - $currentAge;
        } else {
            $yearsToAdulthood = 15 - $currentAge;
        }
        $predictedHeight = $yearsToAdulthood + $heightIncreaseInOneYear;
        $predictedHeightAchieved = $predictedHeight + $nearestHeight;
        $heightFather = $child->user->father_height;
        $heightMother = $child->user->mother_height;
        $predictedHeightMale = ($heightFather + $heightMother + 13) / 2 + 5;
        $predictedHeightFemale = ($heightMother + $heightMother - 13) / 2 + 3;
        if ($gender == Gender::Male) {
            $predictedHeightChild = ($predictedHeightMale * 0.3) + ($predictedHeightAchieved * 0.7);
        } else {
            $predictedHeightChild = ($predictedHeightFemale * 0.3) + ($predictedHeightAchieved * 0.7);
        }
        $who228 = $this->getWho(228, $gender);
        $heightWho = $who228->height;
        $result = ($predictedHeightChild / $heightWho) / 0.1;
        return min($result, 10);

    }

    private function findRatingPQ($childId, $currentDate, $oneYearAgo)
    {
        $ratingPQ = $this->repository->getQueryBuilder()
            ->where('child_id', $childId)
            ->whereDate('assessment_date', '<=', $currentDate)
            ->whereDate('assessment_date', '>=', $oneYearAgo)
            ->orderBy('assessment_date', 'asc')
            ->first();

        if (!$ratingPQ) {
            return $this->repository->getBy(['child_id' => $childId])->first();
        }

        return $ratingPQ;
    }

    /**
     * Tính toán hiệu suất dựa trên giá trị hiện tại, giá trị trong quá khứ, và số ngày giữa hai thời điểm.
     *
     * @param float|int $currentValue Giá trị hiện tại - là giá trị sức mạnh hoặc sức bền được đánh giá gần nhất.
     * @param float|int $pastValue Giá trị cũ - là giá trị sức mạnh hoặc sức bền từ một năm trước.
     * @param int $daysBetween Số ngày giữa ngày đánh giá hiện tại và ngày đánh giá một năm trước.
     * @return float|int Kết quả hiệu suất tính được, được chuẩn hóa không quá 10.
     */
    private function calculatePerformance($currentValue, $pastValue, $daysBetween): float|int
    {
        if ($daysBetween <= 0) {
            return 1;
        }
        $performanceRatio = $daysBetween / 365.3;
        $result = ($currentValue / ($pastValue * 1.25 * $performanceRatio)) / 0.1;
        return min($result, 10);
    }

    public function getEndurance($childId, $currentEndurance): float|int
    {
        $currentDate = Carbon::now()->startOfDay();
        $oneYearAgo = $currentDate->copy()->subYear()->startOfDay();
        $ratingPQ = $this->findRatingPQ($childId, $currentDate, $oneYearAgo);

        if (!$ratingPQ) return 0;

        $daysBetween = $ratingPQ->assessment_date->diffInDays($currentDate);
        return $this->calculatePerformance($currentEndurance, $ratingPQ->endurance, $daysBetween);
    }

    public function getStrength($childId, $currentStrength): float|int
    {
        $currentDate = Carbon::now()->startOfDay();
        $oneYearAgo = $currentDate->copy()->subYear()->startOfDay();
        $ratingPQ = $this->findRatingPQ($childId, $currentDate, $oneYearAgo);

        if (!$ratingPQ) return 0;

        $daysBetween = $ratingPQ->assessment_date->diffInDays($currentDate);
        return $this->calculatePerformance($currentStrength, $ratingPQ->strength, $daysBetween);
    }

    public function getBmiPercent($bmi, $currentBmi): float|int
    {
        $zScore0 = $bmi->z_score_0 ?? 0;
        if ($zScore0 < $currentBmi) {
            return round(($zScore0 / $currentBmi) / 0.1, 1);
        } else {
            return round(($currentBmi / $bmi->z_score_0) / 0.1, 1);
        }
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $height = $data['height'];
        $weight = $data['weight'];
        $child = $this->childRepository->findOrFail($data['child_id']);
        $bmi = $this->calculateBMI($height, $weight);
        $age = $child->age;
        $gender = $child->gender;
        $month = $child->month;
        $who = $this->getWho($month, $gender);
        $whoHeight = $who->height;
        $bmiCategory = $this->getBmiCategory($bmi, $age, $gender);
        $data['bmi'] = $bmi;
        $data['bmi_result'] = $bmiCategory;
        $data['height_change'] = $height - $whoHeight;
        $data['height_result'] = $this->getHeightResult($height, $who);
        return $this->repository->update($data['id'], $data);
    }

    public function getHeightResult($currentHeight, $who): string
    {
        if (!$who) {
            return 'Dữ liệu không xác định';
        }
        $heightWho = $who->height;
        $heightChangeWho = $who->height_change;
        $veryLow = $heightWho - $heightChangeWho * 12;
        $low = $heightWho - $heightChangeWho * 6;
        $slightlyLow = $heightWho - $heightChangeWho * 3;
        $normal = $heightWho;
        $slightlyHigh = $heightWho + $heightChangeWho * 3;
        $high = $heightWho + $heightChangeWho * 6;
        $veryHigh = $heightWho + $heightChangeWho * 12;
        if ($currentHeight <= $veryLow) {
            return 'Rất thấp';
        } elseif ($currentHeight > $veryLow && $currentHeight <= $low) {
            return 'Tương đối thấp';
        } elseif ($currentHeight > $low && $currentHeight <= $slightlyLow) {
            return 'Hơi thấp';
        } elseif ($currentHeight > $slightlyLow && $currentHeight <= $normal) {
            return 'Bình thường';
        } elseif ($currentHeight > $normal && $currentHeight <= $slightlyHigh) {
            return 'Vượt chuẩn';
        } elseif ($currentHeight > $slightlyHigh && $currentHeight <= $high) {
            return 'Tương đối cao';
        } elseif ($currentHeight > $high) {
            return 'Rất cao';
        }
        return 'Không xác định';
    }

    public function getBmiCategory($bmi, $age, $gender): ?string
    {
        $bmiCategory = null;
        if ($age) {
            $bmiThresholds = $this->getBmi($age, $gender);
            if ($bmiThresholds) {
                $bmiCategory = $this->classifyBMI($bmi, $bmiThresholds);
            }
        }
        return $bmiCategory;
    }

    public function calculateBMI($height, $weight): float|int
    {
        if ($height <= 0) return 0;

        $heightInMeters = $height / 100;
        $bmi = $weight / ($heightInMeters * $heightInMeters);

        return round($bmi, 1);
    }

    public function classifyBMI($bmi, $bmiThresholds): string
    {
        if (!$bmiThresholds) return 'Không thể xác định';

        $conditions = [
            'Suy dinh dưỡng' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_minus_3;
            },
            'Quá gầy' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_minus_2;
            },
            'Hơi gầy' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_minus_1;
            },
            'Bình thường' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_0;
            },
            'Hơi béo' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_plus_1;
            },
            'Tương đối béo' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_plus_2;
            },
            'Béo phì' => function ($bmi) {
                return true;
            },
        ];

        foreach ($conditions as $result => $condition) {
            if ($condition($bmi)) {
                return $result;
            }
        }

        return 'Không thể xác định';
    }

    public function getWho($month, $gender)
    {
        return $this->whoRepository->getBy(
            [
                'month' => $month,
                'gender' => $gender,
                'status' => ActiveStatus::Active,
            ]
        )->first();
    }

    public function getBmi($age, $gender)
    {
        return $this->bmiRepository->getBy(
            [
                'age' => $age,
                'gender' => $gender,
                'status' => ActiveStatus::Active,
            ]
        )->first();
    }


    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $this->repository->delete($id);

    }


    public function getOverallStats(Request $request)
    {
        $data = $request->validated();
        $childId = $data['child_id'];
        return $this->repository->getQueryBuilder()
            ->where('child_id', $childId)
            ->orderBy('assessment_date', 'desc')
            ->first();
    }
}
