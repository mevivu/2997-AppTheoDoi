<?php

namespace App\Api\V1\Services\RatingPQ;


use App\Admin\Repositories\Bmi\BmiRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Api\V1\Http\Resources\RatingPQ\RatingPQMonthResource;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Repositories\WeightHeightWho\WhoRepositoryInterface;
use App\Api\V1\Services\HeightPrediction\HeightPredictionServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use App\Models\RatingPQ;
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
        $month = $child->month;
        $who = $this->getWho($month, $gender);
        $whoHeight = $who->height;
        $bmiCategory = $this->getBmiCategory($bmi, $age, $gender, $birthday, $assessmentDate);
        $ageInMonths = $birthday->diffInDays($assessmentDate) / 30.5;
        $data['age_month'] = (int)round($ageInMonths);
        $data['bmi'] = $bmi;
        $data['bmi_result'] = $bmiCategory;
        $data['height_change'] = $height - $whoHeight;
        $data['height_result'] = $this->getHeightResult($height, $birthday, $assessmentDate, $gender);
        $data['score'] = $this->calculateScore($bmi, $age, $gender,
            $child->id, $currentEndurance, $currentStrength, $height, $assessmentDate);

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
        $month = $child->month;
        $birthday = Carbon::parse($child->birthday);
        $who = $this->getWho($month, $gender);
        $whoHeight = $who->height;
        $bmiCategory = $this->getBmiCategory($bmi, $age, $gender, $birthday, $assessmentDate);
        $ageInMonths = $birthday->diffInDays($assessmentDate) / 30.5;
        $data['age_month'] = (int)floor($ageInMonths);
        $data['bmi'] = $bmi;
        $data['bmi_result'] = $bmiCategory;
        $data['height_change'] = $height - $whoHeight;
        $data['height_result'] = $this->getHeightResult($height, $birthday, $assessmentDate, $gender);
        $data['score'] = $this->calculateScore($bmi, $age, $gender,
            $child->id, $currentEndurance, $currentStrength, $height, $assessmentDate);
        return $this->repository->update($data['id'], $data);
    }

    /**
     * @throws Exception
     */
    public function getOverallStats(Request $request, $optionChildId = null): ?array
    {
        $data = $request->validated();
        $childId = $data['child_id'] ?? $optionChildId;
        $ratingLasted = $this->repository->getQueryBuilder()
            ->where('child_id', $childId)
            ->orderBy('assessment_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

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
                'height_who_current' => $heightWhoCurrent,
                'is_taller_than_who' => $currenHeight > $who->height,
            ],
            'current_height_percent' => round($currentHeightPercent, 1),
            'bmi_percent' => round($bmiPercent, 1),
            'endurance_percent' => round($currentEndurancePercent, 1),
            'strength_percent' => round($currentStrengthPercent, 1),
            'height_adulthood' => round($heightAdulthoodPercent, 1),

        ];
    }


    /**
     * @throws Exception
     */
    public function calculateScore($currentBmi, $age, $gender, $childId,
                                   $currentEndurance, $currentStrength, $currenHeight, $assessmentDate): float
    {
        $bmi = $this->getBmi($age, $gender);
        $child = $this->childRepository->findOrFail($childId);
        $bmiPercent = $this->getBmiPercent($bmi, $currentBmi, $child, $assessmentDate);

        $endurancePercent = $currentEndurance ?
            $this->getEndurance($childId, $currentEndurance, null, $assessmentDate) : null;
        $strengthPercent = $currentStrength ?
            $this->getStrength($childId, $currentStrength, null, $assessmentDate) : null;

        $currentHeight = $this->getCurrentHeight($child, $gender);
        $heightAdulthood = $this->getHeightAdulthood($child, $currenHeight, $gender, $assessmentDate);

        $scores = [];

        if ($age > 5) {
            $scores[] = $bmiPercent;
        }

        if (!is_null($endurancePercent)) {
            $scores[] = $endurancePercent;
        }

        if (!is_null($strengthPercent)) {
            $scores[] = $strengthPercent;
        }

        if (!is_null($currentHeight)) {
            $scores[] = $currentHeight;
        }

        if (!is_null($heightAdulthood)) {
            $scores[] = $heightAdulthood;
        }

        if (count($scores) === 0) {
            return 0;
        }

        $totalScore = array_sum($scores);
        return round($totalScore / count($scores), 1);
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

    public function getCurrentHeightPercent($child, $gender)
    {
        $nearestRatingPQ = $this->getLatestPQ($child->id);
        return ($nearestRatingPQ && isset($nearestRatingPQ->height)) ? $nearestRatingPQ->height : null;
    }

    public function getHeightAdulthood($child, $currenHeight, $gender, $assessmentDate)
    {

        $currentDate = $assessmentDate;
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
        $heightFather = $child->user->father_height ?? 0;
        $heightMother = $child->user->mother_height ?? 0;
        $predictedHeightMale = ($heightFather + $heightMother + 13) / 2 + 5;
        $predictedHeightFemale = ($heightFather + $heightMother - 13) / 2 + 3;
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

    /**
     * param float| int currenHeight người dùng nhập
     */
    public function getHeightAdulthoodChart($gender, $predictingAdultHeight)
    {
        $who228 = $this->getWho(228, $gender);
        $heightWho = $who228->height;
        $result = ($predictingAdultHeight / $heightWho) / 0.1;
        return min($result, 10);

    }

    private function findRatingPQ($childId, $currentDate, $oneYearAgo)
    {
        Log::info("Current date: " . $currentDate->toDateTimeString());
        Log::info("One year ago: " . $oneYearAgo->toDateTimeString());
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

    private function getLatestPQ($childId)
    {
        $latest = RatingPQ::where('child_id', $childId)
            ->orderByDesc('assessment_date')
            ->orderBy('id', 'desc')
            ->first();

        if (!$latest) {
            return null;
        }
        return $latest;
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

    public function getEndurance($childId, $currentEndurance, $latestRecordDateCopy = null, $assessmentDate = null): float|int
    {
        if ($latestRecordDateCopy == null) {
            $currentDate = $assessmentDate;
            $oneYearAgo = $currentDate->copy()->subYear()->startOfDay();
            $ratingPQ = $this->findRatingPQ($childId, $currentDate, $oneYearAgo);

            if (!$ratingPQ) return 0;
            if ($ratingPQ->endurance == null) return 0;

            $daysBetween = $ratingPQ->assessment_date->diffInDays($currentDate);
            return $this->calculatePerformance($currentEndurance, $ratingPQ->endurance, $daysBetween);
        } else {
            $oneYearAgo = $latestRecordDateCopy->copy()->subYear();
            $ratingPQ = $this->findRatingPQ($childId, $latestRecordDateCopy, $oneYearAgo);

            if (!$ratingPQ) return 0;
            if ($ratingPQ->endurance == null) return 0;

            $daysBetween = $oneYearAgo->startOfDay()->diffInDays($latestRecordDateCopy->startOfDay());

            return $this->calculatePerformance($currentEndurance, $ratingPQ->endurance, $daysBetween);
        }


    }

    public function getStrength($childId, $currentStrength, $latestRecordDateCopy = null, $assessmentDate = null): float|int
    {
        if ($latestRecordDateCopy == null) {
            $currentDate = $assessmentDate;
            $oneYearAgo = $currentDate->copy()->subYear()->startOfDay();
            $ratingPQ = $this->findRatingPQ($childId, $currentDate, $oneYearAgo);

            if (!$ratingPQ) return 0;
            if ($ratingPQ->strength == null) return 0;

            $daysBetween = $ratingPQ->assessment_date->diffInDays($currentDate);
            return $this->calculatePerformance($currentStrength, $ratingPQ->strength, $daysBetween);
        } else {
            $oneYearAgo = $latestRecordDateCopy->copy()->subYear();
            $ratingPQ = $this->findRatingPQ($childId, $latestRecordDateCopy, $oneYearAgo);

            if (!$ratingPQ) return 0;
            if ($ratingPQ->strength == null) return 0;
            $strengthOneYearAgo = $ratingPQ->strength * 1.25;
            $result = $currentStrength / $strengthOneYearAgo / 0.1;
            return min($result, 10);
        }
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
            $who = $this->whoRepository->getBy(
                [
                    'gender' => $child->gender,
                    'month' => $month
                ])->first();

            if (!$who) return 0;

            return min($currentWeight / $who->weight / 0.1, 10);
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
        $ageThresholds = $birthday->diffInDays($assessmentDate) / 365.3;
        $ageThresholds = round($ageThresholds);
        if ($age) {
            $bmiThresholds = $this->getBmi($ageThresholds, $gender);
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
