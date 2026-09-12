<?php

namespace App\Api\V1\Services\RatingPQ;


use App\Admin\Repositories\Bmi\BmiRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Api\V1\Http\Resources\RatingPQ\RatingPQMonthResource;
use App\Api\V1\Http\Resources\RatingPQ\RatingPQResource;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Repositories\WeightHeightWho\WhoRepositoryInterface;
use App\Api\V1\Services\HeightPrediction\HeightPredictionServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


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

    protected HeightPredictionServiceInterface $heightPredictionService;

    public function __construct(
        RatingPQRepositoryInterface      $repository,
        ChildRepositoryInterface         $childRepository,
        BmiRepositoryInterface           $bmiRepository,
        WhoRepositoryInterface           $whoRepository,
        FileService                      $fileService,
        HeightPredictionServiceInterface $heightPredictionService,
    )
    {
        $this->repository = $repository;
        $this->childRepository = $childRepository;
        $this->bmiRepository = $bmiRepository;
        $this->whoRepository = $whoRepository;
        $this->fileService = $fileService;
        $this->heightPredictionService = $heightPredictionService;
    }

    public function getMonthlyEnduranceData(Request $request): array
    {
        $validated = $request->validated();

        $childId = $validated['child_id'];
//        $month = (int)$validated['month'];
        $year = (int)$validated['year'];

        $records = $this->repository->getQueryBuilder()
            ->where('child_id', $childId)
//            ->whereMonth('assessment_date', '=', $month)
            ->whereYear('assessment_date', '=', $year)
            ->orderBy('assessment_date', 'desc')
            ->get();

        $result = [];
        $groupedRecords = [];

        foreach ($records as $record) {
            $date = $record->assessment_date->toDateString();
            if (!isset($groupedRecords[$date]) || $record->created_at > $groupedRecords[$date]->created_at) {
                $groupedRecords[$date] = $record;
            }
        }

        foreach ($groupedRecords as $record) {
            $result[] = new RatingPQMonthResource($record);
        }

        return $result;
    }


    public function index(Request $request)
    {
        $data = $request->validated();

        $limit = $data['limit'] ?? null;
        $page = $data['page'] ?? 1;

        $query = $this->repository->getQueryBuilder();
        if (!empty($data['child_id'])) {
            $query->where('child_id', $data['child_id']);
        }
        $query->orderBy('assessment_date', 'desc');

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
        $currentEndurance = $data['endurance'] ?? null;
        $currentStrength = $data['strength'] ?? null;
        $child = $this->childRepository->findOrFail($data['child_id']);
        $assessmentDate = Carbon::parse($data['assessment_date']);
        $birthday = Carbon::parse($child->birthday);
        // bmi hien tai
        $bmi = $this->calculateBMI($height, $weight);
        $age = $child->age;
        $gender = $child->gender;
        $bmiCategory = $this->getBmiCategory($bmi, $age, $gender, $birthday, $assessmentDate);
        $monthCalculate = round($birthday->diffInDays($assessmentDate) / 30.5);
        $ageCalculate = round($birthday->diffInDays($assessmentDate) / 365.3);
        $who = $this->getWho($monthCalculate, $gender);
        $whoHeight = $who->height;
        $data['age_month'] = (int)round($monthCalculate);
        $data['bmi'] = $bmi;
        $data['bmi_result'] = $bmiCategory;
        $data['height_change'] = $height - $whoHeight;
        $data['weight_change'] = $who ? round($weight - $who->weight, 2) : null;
        $data['height_result'] = $this->getHeightResult($height, $birthday, $assessmentDate, $gender);
        $data['score'] = $this->calculateScore($bmi, $ageCalculate, $gender,
            $child->id, $currentEndurance, $currentStrength, $height, $assessmentDate, $weight, $monthCalculate);
        return $this->repository->create($data);
    }


    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $height = $data['height'];
        $weight = $data['weight'];
        $currentEndurance = $data['endurance'];
        $currentStrength = $data['strength'];
        $child = $this->childRepository->findOrFail($data['child_id']);
        $assessmentDate = Carbon::parse($data['assessment_date']);
        $bmi = $this->calculateBMI($height, $weight);
        $age = $child->age;
        $gender = $child->gender;
        $birthday = Carbon::parse($child->birthday);

        $bmiCategory = $this->getBmiCategory($bmi, $age, $gender, $birthday, $assessmentDate);
        $monthCalculate = round($birthday->diffInDays($assessmentDate) / 30.5);
        $ageCalculate = round($birthday->diffInDays($assessmentDate) / 365.3);
        $who = $this->getWho($monthCalculate, $gender);
        $whoHeight = $who->height;
        $data['age_month'] = (int)floor($monthCalculate);
        $data['bmi'] = $bmi;
        $data['bmi_result'] = $bmiCategory;
        $data['height_change'] = $height - $whoHeight;
        $data['weight_change'] = $who ? round($weight - $who->weight, 2) : null;
        $data['height_result'] = $this->getHeightResult($height, $birthday, $assessmentDate, $gender);
        $data['score'] = $this->calculateScore($bmi, $ageCalculate, $gender,
            $child->id, $currentEndurance, $currentStrength, $height, $assessmentDate, $weight, $monthCalculate);
        return $this->repository->update($data['id'], $data);
    }


    /**
     * @throws Exception
     */
    public function getOverallStats(Request $request, $optionChildId = null): ?array
    {
        $data = $request->validated();
        $childId = $data['child_id'] ?? $optionChildId;
        $ratingLasted = $this->repository->getLatestByChildId($childId);

        $latestRecordDateCopy = $ratingLasted ? $ratingLasted->assessment_date->copy() : Carbon::now();


        if (!$ratingLasted) {
            return null;
        }
        $child = $this->childRepository->findOrFail($childId);
        $age = $child->age;
        $gender = $child->gender;
        $birthDay = $child->birthday;
        $bmi = $this->getBmi($age, $gender);
        $latestDate = $ratingLasted ? $ratingLasted->assessment_date : Carbon::now();
        $month = round($birthDay->diffInDays($latestDate) / 30.5);
        $who = $this->getWho($month, $gender);

        $currentBmi = $ratingLasted->bmi;
        $currenHeight = $ratingLasted->height;
        $currentEndurance = $ratingLasted->endurance;
        $currentStrength = $ratingLasted->strength;
        $currentWeight = $ratingLasted->weight;

        $predictingAdultHeight = $this->heightPredictionService->calculateMatureHeight($child, $currenHeight, $latestRecordDateCopy);
        $bmiPercent = $this->getBmiPercent($bmi, $currentBmi, $child, $latestRecordDateCopy, $currentWeight);
        $currentEndurancePercent = $this->getEndurance($childId, $currentEndurance, $latestRecordDateCopy);
        $currentStrengthPercent = $this->getStrength($childId, $currentStrength, $latestRecordDateCopy);
        $heightAdulthoodPercent = $this->getHeightAdulthoodChart($gender, $predictingAdultHeight);
        $heightWhoCurrent = round($currenHeight - $who->height, 2);

        $heightCalculate = $currenHeight / $who->height / 0.1;

        $currentHeightPercent = min($heightCalculate, 10);

        return [
            'height' => $currenHeight,
            'weight' => $ratingLasted->weight,
            'strength' => $currentStrength,
            'endurance' => $currentEndurance,
            'bmi' => $currentBmi,
            'bmi_result' => $ratingLasted->bmi_result,
            'height_result' => $ratingLasted->height_result,
            'height_comparison' => [
                'height_who_current' => $who ? $heightWhoCurrent : 0,
                'is_taller_than_who' => $who ? $currenHeight > $who->height : false,
            ],
            'weight_comparison' => [
                'weight_who_current' => $who ? round($currentWeight - $who->weight, 2) : 0,
                'is_heavier_than_who' => $who ? $currentWeight > $who->weight : false,
            ],
            'current_height_percent' => round($currentHeightPercent, 1),
            'bmi_percent' => round($bmiPercent, 1),
            'endurance_percent' => round($currentEndurancePercent, 1),
            'strength_percent' => round($currentStrengthPercent, 1),
            'height_adulthood' => round($heightAdulthoodPercent, 1),

        ];
    }

    public function getScorePQ($request, $childId): ?float
    {
        $overallPQ = $this->getOverallStats($request, $childId);
        $currentHeightPercent = $overallPQ['current_height_percent'] ?? null;
        $bmiPercent = $overallPQ['bmi_percent'] ?? null;
        $strengthPercent = $overallPQ['strength_percent'] ?? null;
        $endurancePercent = $overallPQ['endurance_percent'] ?? null;
        $heightAdulthoodPercent = $overallPQ['height_adulthood'] ?? null;

        $pqComponents = [
            $currentHeightPercent,
            $bmiPercent,
            $strengthPercent,
            $endurancePercent,
            $heightAdulthoodPercent
        ];
        $validPqComponents = array_filter(
            $pqComponents,
            fn($value) => $value !== null && $value != 0
        );
        return count($validPqComponents) > 0 ? round(array_sum($validPqComponents) / count($validPqComponents), 1) : null;

    }

    /**
     * Lấy thông tin tổng hợp thể chất cho màn hình Tổng quan (API V2)
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function getGeneralInfo(Request $request): array
    {
        $data = $request->validated();
        $childId = $data['child_id'];
        $year = (int)($data['year'] ?? Carbon::now()->year);

        $ratingLasted = $this->repository->getLatestByChildId($childId);

        $overall = null;
        $latestRecord = null;

        if ($ratingLasted) {
            $overall = $this->getOverallStats($request, $childId);
            $latestRecord = new RatingPQResource($ratingLasted);
        }

        $records = $this->repository->getQueryBuilder()
            ->where('child_id', $childId)
            ->whereYear('assessment_date', '=', $year)
            ->orderBy('assessment_date', 'desc')
            ->get();

        $groupedRecords = [];
        foreach ($records as $record) {
            $date = $record->assessment_date->toDateString();
            if (!isset($groupedRecords[$date]) || $record->created_at > $groupedRecords[$date]->created_at) {
                $groupedRecords[$date] = $record;
            }
        }

        $statistics = [];
        foreach ($groupedRecords as $record) {
            $statistics[] = new RatingPQMonthResource($record);
        }

        return [
            'overall' => $overall,
            'latest_record' => $latestRecord,
            'statistics' => $statistics,
        ];
    }

    /**
     * @throws Exception
     */
    public function calculateScore($currentBmi, $ageCalculate, $gender, $childId,
                                   $currentEndurance, $currentStrength, $currenHeight,
                                   $assessmentDate, $currentWeight, $monthCalculate): ?float
    {
        $bmi = $this->getBmi($ageCalculate, $gender);
        $who = $this->getWho($monthCalculate, $gender);
        $child = $this->childRepository->findOrFail($childId);
        $predictingAdultHeight = $this->heightPredictionService->calculateMatureHeight($child, $currenHeight, $assessmentDate);
        $bmiPercent = $this->getBmiPercent($bmi, $currentBmi, $child, $assessmentDate, $currentWeight);
        $currentEndurancePercent = $this->getEndurance($childId, $currentEndurance, $assessmentDate);
        $currentStrengthPercent = $this->getStrength($childId, $currentStrength, $assessmentDate);
        $heightAdulthoodPercent = $this->getHeightAdulthoodChart($gender, $predictingAdultHeight);
        $heightCalculate = $currenHeight / $who->height / 0.1;
        $currentHeightPercent = min($heightCalculate, 10);

        $pqComponents = [
            $currentHeightPercent,
            $bmiPercent,
            $currentStrengthPercent,
            $currentEndurancePercent,
            $heightAdulthoodPercent
        ];
        $validPqComponents = array_filter($pqComponents, fn($value) => $value !== null && $value > 0);
        return count($validPqComponents) > 0 ? round(array_sum($validPqComponents) / count($validPqComponents), 1) : null;

    }

    /**
     * param float| int currenHeight người dùng nhập
     */
    public function getHeightAdulthoodChart($gender, $predictingAdultHeight)
    {
        $who228 = $this->getWho(228, $gender);
        if(!$who228) return 0;
        $heightWho = $who228->height;
        $result = ($predictingAdultHeight / $heightWho) / 0.1;
        return min($result, 10);

    }

    private function findRatingPQ($childId, $currentDate, $oneYearAgo)
    {
        Log::info("Current date: " . $currentDate->toDateTimeString());
        Log::info("One year ago: " . $oneYearAgo->toDateTimeString());
        $ratingPQ = $this->repository->getRecordInDateRangeWithValidStrengthEndurance($childId, $oneYearAgo, $currentDate,true);

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
        if ($daysBetween <= 0 || $pastValue <= 0) {
            return 1;
        }

        $performanceRatio = $daysBetween / 365.3;
        $denominator = $pastValue * 1.25 * $performanceRatio;

        if ($denominator == 0) {
            return 1;
        }

        $result = ($currentValue / $denominator) / 0.1;
        return min($result, 10);
    }

    public function getEndurance($childId, $currentEndurance, $latestRecordDateCopy = null): float|int|null
    {
        $oneYearAgo = $latestRecordDateCopy->copy()->subYear();
        $ratingPQ = $this->findRatingPQ($childId, $latestRecordDateCopy, $oneYearAgo);

        if(!$currentEndurance) {
            return null;
        }

        if (!$ratingPQ) {
            return null;
        }
        if ($ratingPQ->endurance == null) {
            return null;
        }

        $daysBetween = $oneYearAgo->startOfDay()->diffInDays($latestRecordDateCopy->startOfDay());

        return $this->calculatePerformance($currentEndurance, $ratingPQ->endurance, $daysBetween);


    }

    public function getStrength($childId, $currentStrength, $latestRecordDateCopy = null)
    {
        $oneYearAgo = $latestRecordDateCopy->copy()->subYear();
        $ratingPQ = $this->findRatingPQ($childId, $latestRecordDateCopy, $oneYearAgo);
        if(!$currentStrength) {
            return null;
        }
        if (!$ratingPQ) return null;
        if ($ratingPQ->strength == null) return null;
        $strengthOneYearAgo = $ratingPQ->strength * 1.25;
        $result = $currentStrength / $strengthOneYearAgo / 0.1;
        return min($result, 10);
    }


    public function getBmiPercent($bmi, $currentBmi, $child, $latestRecordDateCopy, $currentWeight = null): float|int
    {
        $birthday = $child->birthday;
        $ageCalculate = floor($birthday->diffInDays($latestRecordDateCopy) / 365.3);
        $month = round($birthday->diffInDays($latestRecordDateCopy) / 30.5);
        if ($ageCalculate >= 5) {
            $zScore0 = $bmi->z_score_0 ?? 0;
            if ($zScore0 < $currentBmi) {
                return round(($zScore0 / $currentBmi) / 0.1, 1);
            } else {
                return round(($currentBmi / $bmi->z_score_0) / 0.1, 1);
            }
        } else {
            // Trẻ < 5 tuổi: So sánh BMI thực tế với z_score_0 tại mốc 5 tuổi
            $bmi5 = $this->getBmi(5, $child->gender);
            if (!$bmi5) return 0;

            $zScore0 = $bmi5->z_score_0 ?? 0;
            if ($zScore0 <= 0) return 0;

            if ($zScore0 < $currentBmi) {
                return round(($zScore0 / $currentBmi) / 0.1, 1);
            } else {
                return round(($currentBmi / $zScore0) / 0.1, 1);
            }
        }


    }


    public function getHeightResult($currentHeight, $birthday, $assessmentDate, $gender): string
    {
        $month = floor($birthday->diffInDays($assessmentDate) / 30.5);
        $who = $this->getWho($month, $gender);
        if (!$who) {
            return 'Dữ liệu không xác định';
        }
        $heightWho = $who->height;
        $heightChangeWho = $who->height_change;

        $veryLow = $heightWho - $heightChangeWho * 12;
        $low = $heightWho - $heightChangeWho * 6;
        $slightlyLow = $heightWho - $heightChangeWho * 3;
        $slightlyHigh = $heightWho + $heightChangeWho * 3;
        $high = $heightWho + $heightChangeWho * 6;
        $veryHigh = $heightWho + $heightChangeWho * 12;

        if ($currentHeight <= $veryLow) {
            return 'Rất thấp';
        }
        if ($currentHeight <= $low) {
            return 'Thấp';
        }
        if ($currentHeight <= $slightlyLow) {
            return 'Hơi thấp';
        }
        if ($currentHeight <= $slightlyHigh) {
            return 'Bình thường';
        }
        if ($currentHeight <= $high) {
            return 'Vượt chuẩn';
        }
        if ($currentHeight <= $veryHigh) {
            return 'Tương đối cao';
        }
        if ($currentHeight > $veryHigh) {
            return 'Rất cao';
        }
        return 'Không xác định';
    }


    public function getBmiCategory($bmi, $age, $gender, $birthday, $assessmentDate): ?string
    {
        $bmiCategory = null;
        $ageThresholds = $birthday ? round($birthday->diffInDays($assessmentDate) / 365.3) : ($age ? round($age) : 0);
        $genderVal = $gender instanceof Gender ? $gender->value : $gender;

        $bmiThresholds = $this->getBmi($ageThresholds, $genderVal);
        // Fallback: Trẻ < 5 tuổi dùng threshold tại mốc 5 tuổi
        if (!$bmiThresholds && $ageThresholds < 5) {
            $bmiThresholds = $this->getBmi(5, $genderVal);
        }
        if ($bmiThresholds) {
            $bmiCategory = $this->classifyBMI($bmi, $bmiThresholds);
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

        if ($bmi <= $bmiThresholds->z_score_minus_3) {
            return 'Suy dinh dưỡng';
        }
        if ($bmi <= $bmiThresholds->z_score_minus_2) {
            return 'Quá gầy';
        }
        if ($bmi <= $bmiThresholds->z_score_minus_1) {
            return 'Hơi gầy';
        }
        if ($bmi <= $bmiThresholds->z_score_plus_1) {
            return 'Bình thường';
        }
        if ($bmi <= $bmiThresholds->z_score_plus_2) {
            return 'Hơi béo';
        }
        if ($bmi <= $bmiThresholds->z_score_plus_3) {
            return 'Tương đối béo';
        }
        if ($bmi > $bmiThresholds->z_score_plus_3) {
            return 'Béo phì';
        }

        return 'Không thể xác định';
    }


    public function getWho($month, $gender)
    {
        $genderVal = $gender instanceof Gender ? $gender->value : $gender;
        return $this->whoRepository->getBy(
            [
                'month' => $month,
                'gender' => $genderVal,
                'status' => ActiveStatus::Active,
            ]
        )->first();
    }

    public function getBmi($age, $gender)
    {
        $genderVal = $gender instanceof Gender ? $gender->value : $gender;
        return $this->bmiRepository->getBy(
            [
                'age' => $age,
                'gender' => $genderVal,
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
