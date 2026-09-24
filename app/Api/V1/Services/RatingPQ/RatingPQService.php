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
        $whoHeight = $who ? $who->height : 0;
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
        $currentEndurance = $data['endurance'] ?? null;
        $currentStrength = $data['strength'] ?? null;
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
        $whoHeight = $who ? $who->height : 0;
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
        $data = method_exists($request, 'validated') ? $request->validated() : $request->all();
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

        $predictingAdultHeight = $this->heightPredictionService->calculateMatureHeightAt19($child, $currenHeight, $latestRecordDateCopy);
        $bmiPercent = $this->getBmiPercent($bmi, $currentBmi, $child, $latestRecordDateCopy, $currentWeight);
        $currentEndurancePercent = $this->getEndurance($childId, $currentEndurance, $latestRecordDateCopy);
        $currentStrengthPercent = $this->getStrength($childId, $currentStrength, $latestRecordDateCopy);
        $heightAdulthoodPercent = $this->getHeightAdulthoodChart($gender, $predictingAdultHeight);
        $heightWhoCurrent = $who ? round($currenHeight - $who->height, 2) : 0;

        $currentHeightPercent = $who ? $this->mapHeightDeltaToScore((float)$currenHeight, (float)$who->height) : 1;

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
        $predictingAdultHeight = $this->heightPredictionService->calculateMatureHeightAt19($child, $currenHeight, $assessmentDate);
        $bmiPercent = $this->getBmiPercent($bmi, $currentBmi, $child, $assessmentDate, $currentWeight);
        $currentEndurancePercent = $this->getEndurance($childId, $currentEndurance, $assessmentDate);
        $currentStrengthPercent = $this->getStrength($childId, $currentStrength, $assessmentDate);
        $heightAdulthoodPercent = $this->getHeightAdulthoodChart($gender, $predictingAdultHeight);
        $currentHeightPercent = $who ? $this->mapHeightDeltaToScore((float)$currenHeight, (float)$who->height) : 1;

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
     * Tính điểm chiều cao trưởng thành dựa trên độ lệch so với chuẩn WHO mốc 19 tuổi (tháng 228)
     *
     * @param int|Gender $gender Giới tính
     * @param float|int $predictingAdultHeight Chiều cao trưởng thành dự đoán (cm)
     * @return int Điểm số thang 1 - 10
     */
    public function getHeightAdulthoodChart($gender, $predictingAdultHeight): int
    {
        $who228 = $this->getWho(228, $gender);
        if (!$who228 || $who228->height <= 0) {
            $fallbackWho = ($gender == Gender::Male || (is_object($gender) && $gender->value == Gender::Male->value)) ? 176.5 : 163.0;
            return $this->mapHeightDeltaToScore((float)$predictingAdultHeight, (float)$fallbackWho);
        }
        return $this->mapHeightDeltaToScore((float)$predictingAdultHeight, (float)$who228->height);
    }

    /**
     * Quy đổi độ lệch chiều cao so với chuẩn WHO (+/- CM) sang thang điểm 1 - 10
     * Công thức Cột R từ file Excel Diem CC.xlsx:
     * IF(Q>=6,10,IF(Q>=3,9,IF(Q>=0,8,IF(Q>=-2,7,IF(Q>=-4,6,IF(Q>=-7,5,IF(Q>=-9,4,IF(Q>=-11,3,IF(Q>=-13,2,1)))))))))
     *
     * @param float $actualHeight Chiều cao thực tế hoặc chiều cao dự đoán (cm)
     * @param float $whoHeight Chiều cao chuẩn WHO tương ứng (cm)
     * @return int Điểm số thang 1 - 10
     */
    public function mapHeightDeltaToScore(float $actualHeight, float $whoHeight): int
    {
        if ($whoHeight <= 0 || $actualHeight <= 0) {
            return 1;
        }

        $delta = round($actualHeight - $whoHeight, 1);

        return match (true) {
            $delta >= 6.0   => 10,
            $delta >= 3.0   => 9,
            $delta >= 0.0   => 8,
            $delta >= -2.0  => 7,
            $delta >= -4.0  => 6,
            $delta >= -7.0  => 5,
            $delta >= -9.0  => 4,
            $delta >= -11.0 => 3,
            $delta >= -13.0 => 2,
            default         => 1,
        };
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

        // Lấy BMI chuẩn theo độ tuổi thực tế (hỗ trợ đầy đủ từ 1 đến 19 tuổi)
        $targetAge = max(1, min(19, (int)$ageCalculate));
        $bmiRecord = ($ageCalculate == $child->age && $bmi) ? $bmi : $this->getBmi($targetAge, $child->gender);
        $zScore0 = $bmiRecord ? (float)($bmiRecord->z_score_0 ?? 0) : 0;

        if ($zScore0 > 0 && $currentBmi > 0) {
            if ($zScore0 < $currentBmi) {
                return round(($zScore0 / $currentBmi) / 0.1, 1);
            } else {
                return round(($currentBmi / $zScore0) / 0.1, 1);
            }
        }

        return 0;
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
        // Fallback: Trẻ < 1 tuổi dùng threshold tại mốc 1 tuổi
        if (!$bmiThresholds && $ageThresholds < 1) {
            $bmiThresholds = $this->getBmi(1, $genderVal);
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
        $safeMonth = max(0, min((int)$month, 228));
        return $this->whoRepository->getBy(
            [
                'month' => $safeMonth,
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

    /**
     * Trả về toàn bộ dữ liệu bóc tách chẩn đoán chi tiết cho 5 chỉ số thể chất (PQ Radar Chart)
     *
     * @param int $childId
     * @return array
     * @throws Exception
     */
    public function debugPqCalculation(int $childId): array
    {
        $child = $this->childRepository->findOrFail($childId);
        $ratingLasted = $this->repository->getLatestByChildId($childId);

        $genderVal = $child->gender instanceof Gender ? $child->gender->value : $child->gender;
        $genderText = $genderVal == 1 ? 'Nam' : 'Nữ';
        $birthday = $child->birthday ? Carbon::parse($child->birthday) : null;

        if (!$ratingLasted) {
            return [
                'has_data' => false,
                'message' => 'Trẻ chưa có bản ghi đo thể chất PQ nào.',
                'child_info' => [
                    'id' => $child->id,
                    'name' => $child->fullname,
                    'gender' => $genderText,
                    'birthday' => $birthday ? $birthday->format('d/m/Y') : '--',
                ],
            ];
        }

        $latestDate = $ratingLasted->assessment_date ? $ratingLasted->assessment_date->copy() : Carbon::now();
        $daysLived = $birthday ? $birthday->diffInDays($latestDate) : 0;
        $monthsLived = round($daysLived / 30.5);
        $yearsLived = round($daysLived / 365.3, 1);
        $intAge = (int)floor($daysLived / 365.3);

        // 1. Chiều cao hiện tại
        $currenHeight = (float)$ratingLasted->height;
        $whoCurrent = $this->getWho($monthsLived, $child->gender);
        $whoCurrentHeight = $whoCurrent ? (float)$whoCurrent->height : 0;
        $deltaCurrentHeight = $whoCurrent ? round($currenHeight - $whoCurrentHeight, 1) : 0;
        $scoreCurrentHeight = $whoCurrent ? $this->mapHeightDeltaToScore($currenHeight, $whoCurrentHeight) : 1;

        $ruleCurrentHeight = match (true) {
            $deltaCurrentHeight >= 6.0   => "Độ lệch Δ >= +6.0 cm => 10 điểm",
            $deltaCurrentHeight >= 3.0   => "Độ lệch Δ từ +3.0 đến < +6.0 cm => 9 điểm",
            $deltaCurrentHeight >= 0.0   => "Độ lệch Δ từ 0.0 đến < +3.0 cm => 8 điểm",
            $deltaCurrentHeight >= -2.0  => "Độ lệch Δ từ -2.0 đến < 0.0 cm => 7 điểm",
            $deltaCurrentHeight >= -4.0  => "Độ lệch Δ từ -4.0 đến < -2.0 cm => 6 điểm",
            $deltaCurrentHeight >= -7.0  => "Độ lệch Δ từ -7.0 đến < -4.0 cm => 5 điểm",
            $deltaCurrentHeight >= -9.0  => "Độ lệch Δ từ -9.0 đến < -7.0 cm => 4 điểm",
            $deltaCurrentHeight >= -11.0 => "Độ lệch Δ từ -11.0 đến < -9.0 cm => 3 điểm",
            $deltaCurrentHeight >= -13.0 => "Độ lệch Δ từ -13.0 đến < -11.0 cm => 2 điểm",
            default                      => "Độ lệch Δ < -13.0 cm => 1 điểm",
        };

        // 2. Chiều cao trưởng thành (V2)
        $predictingAdultHeight = $this->heightPredictionService->calculateMatureHeightAt19($child, $currenHeight, $latestDate);
        $who228 = $this->getWho(228, $child->gender);
        $whoAdultStandard = ($who228 && $who228->height > 0) ? (float)$who228->height : ($genderVal == 1 ? 176.5 : 163.0);
        $deltaAdultHeight = round($predictingAdultHeight - $whoAdultStandard, 1);
        $scoreAdultHeight = $this->getHeightAdulthoodChart($child->gender, $predictingAdultHeight);

        $ruleAdultHeight = match (true) {
            $deltaAdultHeight >= 6.0   => "Độ lệch Δ >= +6.0 cm => 10 điểm",
            $deltaAdultHeight >= 3.0   => "Độ lệch Δ từ +3.0 đến < +6.0 cm => 9 điểm",
            $deltaAdultHeight >= 0.0   => "Độ lệch Δ từ 0.0 đến < +3.0 cm => 8 điểm",
            $deltaAdultHeight >= -2.0  => "Độ lệch Δ từ -2.0 đến < 0.0 cm => 7 điểm",
            $deltaAdultHeight >= -4.0  => "Độ lệch Δ từ -4.0 đến < -2.0 cm => 6 điểm",
            $deltaAdultHeight >= -7.0  => "Độ lệch Δ từ -7.0 đến < -4.0 cm => 5 điểm",
            $deltaAdultHeight >= -9.0  => "Độ lệch Δ từ -9.0 đến < -7.0 cm => 4 điểm",
            $deltaAdultHeight >= -11.0 => "Độ lệch Δ từ -11.0 đến < -9.0 cm => 3 điểm",
            $deltaAdultHeight >= -13.0 => "Độ lệch Δ từ -13.0 đến < -11.0 cm => 2 điểm",
            default                    => "Độ lệch Δ < -13.0 cm => 1 điểm",
        };

        // 3. BMI / Cân nặng
        $currentWeight = (float)$ratingLasted->weight;
        $currentBmi = (float)$ratingLasted->bmi;
        if ($currentBmi <= 0 && $currenHeight > 0 && $currentWeight > 0) {
            $currentBmi = $this->calculateBMI($currenHeight, $currentWeight);
        }
        $targetAge = max(1, min(19, $intAge));
        $bmiRecord = $this->getBmi($targetAge, $child->gender);
        $zScore0 = $bmiRecord ? (float)($bmiRecord->z_score_0 ?? 0) : 0;
        $scoreBmi = $this->getBmiPercent($bmiRecord, $currentBmi, $child, $latestDate, $currentWeight);
        $bmiCategory = $ratingLasted->bmi_result ?? $this->getBmiCategory($currentBmi, $intAge, $child->gender, $birthday, $latestDate);

        // 4. Sức mạnh cơ bắp
        $currentStrength = $ratingLasted->strength !== null ? (float)$ratingLasted->strength : null;
        $oneYearAgo = $latestDate->copy()->subYear();
        $pastRecordStrength = $this->findRatingPQ($childId, $latestDate, $oneYearAgo);
        $pastStrength = ($pastRecordStrength && $pastRecordStrength->strength !== null) ? (float)$pastRecordStrength->strength : null;
        $scoreStrength = $this->getStrength($childId, $currentStrength, $latestDate);
        $expectedStrength = $pastStrength ? round($pastStrength * 1.25, 2) : null;

        // 5. Sức bền vận động
        $currentEndurance = $ratingLasted->endurance !== null ? (float)$ratingLasted->endurance : null;
        $pastRecordEndurance = $this->findRatingPQ($childId, $latestDate, $oneYearAgo);
        $pastEndurance = ($pastRecordEndurance && $pastRecordEndurance->endurance !== null) ? (float)$pastRecordEndurance->endurance : null;
        $scoreEndurance = $this->getEndurance($childId, $currentEndurance, $latestDate);
        $daysBetween = $oneYearAgo->startOfDay()->diffInDays($latestDate->startOfDay());
        $performanceRatio = round($daysBetween / 365.3, 3);
        $denominatorEndurance = $pastEndurance ? round($pastEndurance * 1.25 * ($daysBetween / 365.3), 2) : 0;

        // 6. Điểm tổng hợp PQ
        $components = [
            'current_height' => round($scoreCurrentHeight, 1),
            'adulthood_height' => round($scoreAdultHeight, 1),
            'bmi' => round($scoreBmi, 1),
            'strength' => $scoreStrength !== null ? round($scoreStrength, 1) : null,
            'endurance' => $scoreEndurance !== null ? round($scoreEndurance, 1) : null,
        ];
        $validComponents = array_filter($components, fn($val) => $val !== null && $val > 0);
        $calculatedOverallScore = count($validComponents) > 0 ? round(array_sum($validComponents) / count($validComponents), 1) : null;

        // 7. Lịch sử các lần đo PQ
        $historyRecords = $this->repository->getQueryBuilder()
            ->where('child_id', $childId)
            ->orderBy('assessment_date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'assessment_date' => $r->assessment_date ? $r->assessment_date->format('d/m/Y') : '--',
                    'age_month' => $r->age_month,
                    'height' => $r->height,
                    'weight' => $r->weight,
                    'bmi' => $r->bmi,
                    'bmi_result' => $r->bmi_result ?? '--',
                    'strength' => $r->strength,
                    'endurance' => $r->endurance,
                    'score' => $r->score,
                ];
            });

        return [
            'has_data' => true,
            'child_info' => [
                'id' => $child->id,
                'name' => $child->fullname,
                'gender' => $genderText,
                'birthday' => $birthday ? $birthday->format('d/m/Y') : '--',
                'days_lived' => $daysLived,
                'months_lived' => $monthsLived,
                'years_lived' => $yearsLived,
                'int_age' => $intAge,
            ],
            'latest_pq' => [
                'id' => $ratingLasted->id,
                'assessment_date' => $ratingLasted->assessment_date ? $ratingLasted->assessment_date->format('d/m/Y') : '--',
                'height' => $currenHeight,
                'weight' => $currentWeight,
                'bmi' => $currentBmi,
                'strength' => $currentStrength,
                'endurance' => $currentEndurance,
                'db_score' => $ratingLasted->score,
            ],
            'metrics_breakdown' => [
                'current_height' => [
                    'actual_height' => $currenHeight,
                    'who_month' => (int)$monthsLived,
                    'who_standard_height' => $whoCurrentHeight,
                    'delta' => $deltaCurrentHeight,
                    'excel_formula' => '=IF(Q>=6,10,IF(Q>=3,9,IF(Q>=0,8,IF(Q>=-2,7,IF(Q>=-4,6,IF(Q>=-7,5,IF(Q>=-9,4,IF(Q>=-11,3,IF(Q>=-13,2,1)))))))))',
                    'rule_applied' => $ruleCurrentHeight,
                    'score' => $scoreCurrentHeight,
                ],
                'adulthood_height' => [
                    'predicted_height' => round($predictingAdultHeight, 1),
                    'who_month' => 228,
                    'who_adult_standard' => $whoAdultStandard,
                    'delta' => $deltaAdultHeight,
                    'excel_formula' => '=IF(Q>=6,10,IF(Q>=3,9,IF(Q>=0,8,IF(Q>=-2,7,IF(Q>=-4,6,IF(Q>=-7,5,IF(Q>=-9,4,IF(Q>=-11,3,IF(Q>=-13,2,1)))))))))',
                    'rule_applied' => $ruleAdultHeight,
                    'score' => $scoreAdultHeight,
                ],
                'bmi' => [
                    'actual_weight' => $currentWeight,
                    'actual_height' => $currenHeight,
                    'bmi_value' => $currentBmi,
                    'target_age' => $targetAge,
                    'who_z_score_0' => $zScore0,
                    'classification' => $bmiCategory,
                    'thresholds' => $bmiRecord ? [
                        'z_score_minus_3' => (float)$bmiRecord->z_score_minus_3,
                        'z_score_minus_2' => (float)$bmiRecord->z_score_minus_2,
                        'z_score_minus_1' => (float)$bmiRecord->z_score_minus_1,
                        'z_score_0' => (float)$bmiRecord->z_score_0,
                        'z_score_plus_1' => (float)$bmiRecord->z_score_plus_1,
                        'z_score_plus_2' => (float)$bmiRecord->z_score_plus_2,
                        'z_score_plus_3' => (float)$bmiRecord->z_score_plus_3,
                    ] : null,
                    'formula' => $zScore0 < $currentBmi ? "round(($zScore0 / $currentBmi) / 0.1, 1)" : "round(($currentBmi / $zScore0) / 0.1, 1)",
                    'calc_details' => $zScore0 > 0 && $currentBmi > 0 ? ($zScore0 < $currentBmi ? "({$zScore0} / {$currentBmi}) / 0.1 = " . round(($zScore0 / $currentBmi) / 0.1, 1) : "({$currentBmi} / {$zScore0}) / 0.1 = " . round(($currentBmi / $zScore0) / 0.1, 1)) : '--',
                    'score' => round($scoreBmi, 1),
                ],
                'strength' => [
                    'current_value' => $currentStrength,
                    'past_record_id' => $pastRecordStrength?->id,
                    'past_record_date' => $pastRecordStrength?->assessment_date ? $pastRecordStrength->assessment_date->format('d/m/Y') : '--',
                    'past_value' => $pastStrength,
                    'expected_growth_1_year' => $expectedStrength,
                    'formula' => $pastStrength ? "min(($currentStrength / ($pastStrength * 1.25)) / 0.1, 10)" : "Không có bản ghi đối chiếu",
                    'calc_details' => ($currentStrength !== null && $pastStrength) ? "({$currentStrength} / {$expectedStrength}) / 0.1 = " . ($scoreStrength !== null ? round($scoreStrength, 1) : '--') : '--',
                    'score' => $scoreStrength !== null ? round($scoreStrength, 1) : null,
                ],
                'endurance' => [
                    'current_value' => $currentEndurance,
                    'past_record_id' => $pastRecordEndurance?->id,
                    'past_record_date' => $pastRecordEndurance?->assessment_date ? $pastRecordEndurance->assessment_date->format('d/m/Y') : '--',
                    'past_value' => $pastEndurance,
                    'days_between' => $daysBetween,
                    'performance_ratio' => $performanceRatio,
                    'denominator' => $denominatorEndurance,
                    'formula' => $pastEndurance ? "min(($currentEndurance / ($pastEndurance * 1.25 * ($daysBetween / 365.3))) / 0.1, 10)" : "Không có bản ghi đối chiếu",
                    'calc_details' => ($currentEndurance !== null && $denominatorEndurance > 0) ? "({$currentEndurance} / {$denominatorEndurance}) / 0.1 = " . ($scoreEndurance !== null ? round($scoreEndurance, 1) : '--') : '--',
                    'score' => $scoreEndurance !== null ? round($scoreEndurance, 1) : null,
                ],
                'overall_pq' => [
                    'components' => $components,
                    'valid_count' => count($validComponents),
                    'formula' => count($validComponents) > 0 ? "(" . implode(' + ', array_values($validComponents)) . ") / " . count($validComponents) : '--',
                    'calculated_score' => $calculatedOverallScore,
                    'stored_score' => $ratingLasted->score,
                ],
            ],
            'history_records' => $historyRecords,
        ];
    }
}

