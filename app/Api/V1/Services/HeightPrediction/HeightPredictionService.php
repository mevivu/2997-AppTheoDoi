<?php

namespace App\Api\V1\Services\HeightPrediction;


use App\Api\V1\Http\Resources\Child\ChildResource;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Repositories\WeightHeightWho\WhoRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class HeightPredictionService implements HeightPredictionServiceInterface
{
    use  AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected RatingPQRepositoryInterface $repository;
    protected ChildRepositoryInterface $childRepository;
    protected WhoRepositoryInterface $whoRepository;

    public function __construct(
        RatingPQRepositoryInterface $repository,
        ChildRepositoryInterface    $childRepository,
        WhoRepositoryInterface      $whoRepository,
    )
    {
        $this->repository = $repository;
        $this->childRepository = $childRepository;
        $this->whoRepository = $whoRepository;
    }

    /**
     * @throws Exception
     */
    public function index(Request $request): array
    {
        $data = $request->validated();
        $childId = $data['child_id'];

        $child = $this->childRepository->findOrFail($childId);
        $birthDay = $child->birthday;
        $gender = $child->gender;

        // Lấy bản ghi mới nhất của trẻ
        $latestRecord = $this->repository->getLatestByChildId($childId);
        $latestRecordDateCopy = $latestRecord ? $latestRecord->assessment_date->copy() : Carbon::now();

        $currentHeight = $latestRecord ? $latestRecord->height : 0;

        // Lấy ngày đánh giá mới nhất hoặc ngày hiện tại nếu không có bản ghi
        $latestDate = $latestRecord ? $latestRecord->assessment_date : Carbon::now();
        // Tính tháng từ ngày sinh đến latestDate
        $month = round($birthDay->diffInDays($latestDate) / 30.5);

        // Tính sự thay đổi chiều cao
        $resultSpeedHeightChange = $this->calculateSpeedHeightChange($currentHeight, $childId, $latestDate);
        $heightChange = $resultSpeedHeightChange['height_change'];
        $oldestRecord = $resultSpeedHeightChange['oldest_record'];
        $oldestRecordExists = (bool)$oldestRecord;

        $heightChangeLasted = $latestRecord ? $latestRecord->height : 0;


        // Lấy thông tin WHO cho độ tuổi và giới tính
        $who = $this->getWho($month, $gender);
        $heightChangeWho = $who->height_change;

        $adviceMessage = $this->getAdviceMessage($heightChange, $heightChangeWho);
        $predictingAdultHeight = $this->calculateMatureHeight($child, $currentHeight, $latestRecordDateCopy);

        $heightWhoCurrent = round($heightChangeLasted - $who->height, 2);


        $growthEvaluation = $this->evaluateHeightGrowth($heightChange, $heightChangeWho);

        return [
            'advice_message' => $adviceMessage,
            'oldest_record_exists' => $oldestRecordExists,
            'speed_change' => $heightChange,
            'predicting_adult_height' => $predictingAdultHeight,
            'height_comparison' => [
                'height_who_current' => $heightWhoCurrent,
                'is_taller_than_who' => $heightWhoCurrent > 0,
                'message' => $growthEvaluation['message'],
            ],
            'child' => new ChildResource($child)
        ];
    }

    public function calculateMatureHeight($child, $currentHeight, $latestDate): float
    {

        $heightFather = $child->user->father_height ?? 0;
        $heightMother = $child->user->mother_height ?? 0;
        $birthday = $child->birthday;
        $Adulthood = $child->gender == Gender::Male ? 16 : 15;

        $oneYearBefore = $latestDate->copy()->subYear();


        $oldestRecord = $this->repository->getRecordInDateRange($child->id, $oneYearBefore, $latestDate, true);

        $currentAge = $latestDate->diffInDays($birthday) / 365.3;

        $predictAdulthood = $Adulthood - $currentAge;

        $heightOneYearAgo = $oldestRecord ? $oldestRecord->height : 0;
        $countDays = $latestDate->diffInDays($oldestRecord ? $oldestRecord->assessment_date : $latestDate);
        if ($countDays == 0 || !$oldestRecord) {
            $increasedHeight = $currentHeight - $heightOneYearAgo;
        } else {
            $increasedHeight = ($currentHeight - $heightOneYearAgo) * (365.3 / $countDays);
        }
        $increasedHeight = max(0, min(7, $increasedHeight));


        $adultHeightPrediction = $predictAdulthood * $increasedHeight;


        $predictedHeightMale = ($heightFather + $heightMother + 13) / 2 + 5;
        $predictedHeightFemale = ($heightFather + $heightMother - 13) / 2 + 3;
        if ($child->age >= 5) {
            if ($increasedHeight == 0) {
                if ($child->gender == Gender::Male) {
                    return max($currentHeight, $predictedHeightMale);
                } else {
                    return max($currentHeight, $predictedHeightFemale);
                }
            }

            $CurrentHeightAttainmentForecast = $currentHeight + $adultHeightPrediction;
        } else {
            $ageCheckMonth = $child->gender == Gender::Male ? 24 : 18;
            $ratingPq = $this->repository->getQueryBuilder()
                ->where('child_id', $child->id)
                ->where('age_month', $ageCheckMonth)
                ->first();
            if ($ratingPq) {
                $CurrentHeightAttainmentForecast = $ratingPq->height * 2;
            } else {
                if ($child->gender == Gender::Male) {
                    $CurrentHeightAttainmentForecast = $predictedHeightMale;
                } else {
                    $CurrentHeightAttainmentForecast = $predictedHeightFemale;
                }

            }
        }

        $responseHeightParent = $child->gender == Gender::Male ? $predictedHeightMale * 0.3 : $predictedHeightFemale * 0.3;

        return round(($CurrentHeightAttainmentForecast * 0.7) + $responseHeightParent, 0);
    }


    public function calculateSpeedHeightChange($currentHeight, $childId, $latestDate): array
    {
        $oneYearBefore = $latestDate->copy()->subYear();

        $oldestRecord = $this->repository->getRecordInDateRange($childId, $oneYearBefore, $latestDate, true);

        $countDays = $latestDate->diffInDays($oldestRecord ? $oldestRecord->assessment_date : $latestDate);
        if ($countDays == 0) {
            return [
                'height_change' => 0,
                'oldest_record' => $oldestRecord
            ];
        }

        $oldestHeight = $oldestRecord ? $oldestRecord->height : 0;

        $heightChange = round(($currentHeight - $oldestHeight) * (365.3 / $countDays), 1);
        return [
            'height_change' => $heightChange,
            'oldest_record' => $oldestRecord
        ];
    }


    public function getAdviceMessage($heightChange, $heightChangeWho): string
    {
        // Kiểm tra nếu dữ liệu bằng 0 (chưa có sự tăng trưởng hoặc chưa nhập dữ liệu)
        if ($heightChange == 0) {
            return "Bạn hãy cung cấp thêm dữ liệu chiều cao của con bạn trong vòng 6-12 tháng trước để có dự đoán và đánh giá chiều cao chính xác hơn.";
        }

        $result = $heightChangeWho * 0.75 * 12;

        if ($heightChange < $result) {
            return "Bố mẹ cần thay đổi chế độ dinh dưỡng và vận động cho con hoặc tốt nhất là đi khám bác sĩ dinh dưỡng.";
        } else {
            return "Bố mẹ nên duy trì hoặc làm tốt hơn chế độ dinh dưỡng và vận động cho con như hiện tại.";
        }
    }

    public function evaluateHeightGrowth($heightChange, $heightChangeWho): array
    {
        $result1 = $heightChangeWho * 0.75 * 12;
        $result2 = $heightChangeWho * 1.25 * 12;

        $isGoodGrowth = $heightChange >= $result1;

        if ($heightChange == 0) {
            $message = "Bạn hãy cung cấp thêm dữ liệu chiều cao của con bạn trong vòng 6-12 tháng trước để có dự đoán và đánh giá chiều cao chính xác hơn.";
        } elseif ($heightChange < $result1) {
            $message = "Tốc độ tăng chiều cao của con đang thấp hơn so với chuẩn WHO";
        } elseif ($heightChange > $result2) {
            $message = "Tốc độ tăng chiều cao của con đang cao hơn so với chuẩn WHO";
        } else {
            $message = "Tốc độ tăng chiều cao của con đang đạt chuẩn WHO";
        }

        return [
            'status' => $isGoodGrowth,
            'message' => $message,
        ];
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

    /**
     * Tính toán dữ liệu biểu đồ phác đồ chiều cao (3 đường: Dự đoán, Chuẩn WHO, Mục tiêu)
     */
    public function chart(Request $request): array
    {
        $data = $request->validated();
        $childId = $data['child_id'];
        $targetHeight = (float)$data['target_height'];
        $pubertyMonths = isset($data['puberty_months']) ? (float)$data['puberty_months'] : 0.0;

        $child = $this->childRepository->findOrFail($childId);
        $birthday = $child->birthday;
        $gender = $child->gender;

        // Lấy bản ghi mới nhất của trẻ
        $latestRecord = $this->repository->getLatestByChildId($childId);
        $latestRecordDateCopy = $latestRecord ? $latestRecord->assessment_date->copy() : Carbon::now();
        $currentHeight = $latestRecord ? (float)$latestRecord->height : 0.0;
        $latestDate = $latestRecord ? $latestRecord->assessment_date : Carbon::now();

        // Tuổi hiện tại (năm)
        $currentAge = round($latestDate->diffInDays($birthday) / 365.3, 1);

        // Tính tốc độ tăng trưởng hiện tại
        $resultSpeedHeightChange = $this->calculateSpeedHeightChange($currentHeight, $childId, $latestDate);
        $rawSpeed = (float)$resultSpeedHeightChange['height_change'];
        $increasedHeight = max(0.0, min(7.0, $rawSpeed));

        // Nếu tốc độ tăng = 0, fallback theo mức tăng trung bình WHO của lứa tuổi hiện tại
        if ($increasedHeight <= 0) {
            $whoCurrent = $this->getWho(round($currentAge * 12), $gender);
            $effectiveSpeed = $whoCurrent && $whoCurrent->height_change ? (float)$whoCurrent->height_change * 12 : 5.5;
        } else {
            $effectiveSpeed = $increasedHeight;
        }

        // Tuổi kết thúc dậy thì (Adulthood kết thúc tăng vọt)
        $basePubertyEndAge = ($gender == Gender::Male ? 14.0 : 13.0);
        if ($pubertyMonths > 0) {
            $pubertyStartAge = $currentAge - ($pubertyMonths / 12.0);
            $pubertyDuration = ($gender == Gender::Male ? 3.0 : 2.5);
            $pubertyEndAge = round($pubertyStartAge + $pubertyDuration, 1);
            $pubertyEndAge = max($currentAge, $pubertyEndAge);
        } else {
            $pubertyEndAge = $basePubertyEndAge;
        }
        if ($pubertyEndAge < $currentAge) {
            $pubertyEndAge = $currentAge;
        }

        // Dự đoán chiều cao trưởng thành theo V1
        $predictedAdultHeight = $this->calculateMatureHeight($child, $currentHeight, $latestRecordDateCopy);

        // Khoảng tuổi hiển thị trên biểu đồ: từ (floor(currentAge) + 1) đến 19 tuổi
        $startAge = (int)floor($currentAge) + 1;
        if ($startAge < 5) {
            $startAge = 5;
        }
        if ($startAge > 18) {
            $startAge = 18;
        }
        $maxAge = 19;

        // 0. Mốc "Hiện tại"
        $whoCurrentRecord = $this->getWho(round($currentAge * 12), $gender);
        $whoCurrentHeight = $whoCurrentRecord ? (float)$whoCurrentRecord->height : $currentHeight;

        // 1. Lấy đường Chuẩn WHO cho các mốc tuổi
        $whoHeights = [];
        $whoLine = [
            [
                'age' => (float)$currentAge,
                'height' => round($whoCurrentHeight, 1),
                'is_current' => true,
            ],
        ];
        for ($age = $startAge; $age <= $maxAge; $age++) {
            $month = $age * 12;
            $who = $this->getWho($month, $gender);
            $h = $who ? (float)$who->height : 0.0;
            $whoHeights[$age] = $h;
            $whoLine[] = [
                'age' => (float)$age,
                'height' => round($h, 1),
                'is_current' => false,
            ];
        }

        // 2. Tính đường DỰ ĐOÁN
        $predictionLine = [
            [
                'age' => (float)$currentAge,
                'height' => round($currentHeight, 1),
                'is_current' => true,
            ],
        ];
        $predictionHeights = [];
        $prevPredHeight = $currentHeight;
        $predAtPubertyEnd = $currentHeight + max(0.0, $pubertyEndAge - $currentAge) * $effectiveSpeed;

        for ($age = $startAge; $age <= $maxAge; $age++) {
            if ($age <= $pubertyEndAge) {
                $predH = $currentHeight + ($age - $currentAge) * $effectiveSpeed;
            } else {
                // Sau tuổi hết dậy thì: lấy mốc trước cộng phần tăng nhỏ của WHO
                $whoDelta = 0.2;
                if (isset($whoHeights[$age], $whoHeights[$age - 1])) {
                    $whoDelta = max(0.0, $whoHeights[$age] - $whoHeights[$age - 1]);
                }
                $predH = $prevPredHeight + $whoDelta;
            }
            $prevPredHeight = $predH;
            $predictionHeights[$age] = $predH;
            $predictionLine[] = [
                'age' => (float)$age,
                'height' => round($predH, 1),
                'is_current' => false,
            ];
        }

        // 3. Tính đường MỤC TIÊU
        // Luôn dùng targetHeight do người dùng nhập (không dùng max())
        // để đường mục tiêu luôn khác biệt so với đường dự đoán.
        $remainingYears = max(0.5, $pubertyEndAge - $currentAge);

        // Tính chiều cao mục tiêu ở tuổi hết dậy thì bằng nội suy tuyến tính
        // từ currentHeight → targetHeight theo tỷ lệ thời gian
        $maxGrowthAge = 19;
        $totalYears = max(1, $maxGrowthAge - $currentAge);

        $targetLine = [
            [
                'age' => (float)$currentAge,
                'height' => round($currentHeight, 1),
                'is_current' => true,
            ],
        ];
        for ($age = $startAge; $age <= $maxAge; $age++) {
            // Nội suy tuyến tính từ currentHeight → targetHeight
            $progress = ($age - $currentAge) / $totalYears;
            $progress = min(1.0, max(0.0, $progress));
            $tgtH = $currentHeight + ($targetHeight - $currentHeight) * $progress;

            // Sau tuổi hết dậy thì, giữ nguyên chiều cao mục tiêu (plateau)
            if ($age >= $maxAge) {
                $tgtH = $targetHeight;
            }

            $targetLine[] = [
                'age' => (float)$age,
                'height' => round($tgtH, 1),
                'is_current' => false,
            ];
        }

        return [
            'child' => new ChildResource($child),
            'current_age' => $currentAge,
            'current_height' => $currentHeight,
            'speed_change' => $rawSpeed,
            'predicted_adult_height' => $predictedAdultHeight,
            'puberty_end_age' => $pubertyEndAge,
            'target_height' => $targetHeight,
            'puberty_months' => $pubertyMonths,
            'prediction_line' => $predictionLine,
            'who_line' => $whoLine,
            'target_line' => $targetLine,
        ];
    }
}
