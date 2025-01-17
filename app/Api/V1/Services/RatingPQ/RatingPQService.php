<?php

namespace App\Api\V1\Services\RatingPQ;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\BMI\BMIRepositoryInterface;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Repositories\WeightHeightWho\WhoRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\ActiveStatus;
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
    protected BMIRepositoryInterface $bmiRepository;

    protected WhoRepositoryInterface $whoRepository;
    protected FileService $fileService;

    public function __construct(
        RatingPQRepositoryInterface $repository,
        ChildRepositoryInterface    $childRepository,
        BMIRepositoryInterface      $bmiRepository,
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


    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;

        $query = $this->repository->getByQueryBuilder([
            'child_id' => $data['child_id'],
        ]);
        return $query->paginate($limit, ['*'], 'page', $page);
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
        $this->calculateScore($bmi, $age, $gender, $child->id, $currentEndurance, $currentStrength);
        return $this->repository->create($data);
    }

    public function calculateScore($currentBmi, $age, $gender, $childId, $currentEndurance, $currentStrength): void
    {
        $bmi = $this->getBmi($age, $gender);
        $bmiPercent = $this->getBmiPercent($bmi, $currentBmi);
        $endurancePercent = $this->getEndurance($childId, $currentEndurance);
        $strengthPercent = $this->getStrength($childId, $currentStrength);

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
        $zScore0 = $bmi->z_score_0;
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
        $baseLow = ($heightWho - $heightChangeWho);
        $baseHigh = ($heightWho + $heightChangeWho);
        $veryLow = $baseLow * 12;
        $low = $baseLow * 6;
        $slightlyLow = $baseLow * 3;
        $normal = $baseLow;
        $slightlyHigh = $baseHigh * 3;
        $high = $baseHigh * 6;
        $veryHigh = $baseHigh * 12;
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


}
