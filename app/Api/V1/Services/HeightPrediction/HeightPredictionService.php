<?php

namespace App\Api\V1\Services\HeightPrediction;


use App\Api\V1\Http\Resources\Child\ChildResource;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Repositories\WeightHeightWho\WhoRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use App\Models\Bmi;
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

        $pubertyMonths = isset($data['puberty_months']) ? (float)$data['puberty_months'] : 0.0;

        $adviceMessage = $this->getAdviceMessage($heightChange, $heightChangeWho);
        $predictingAdultHeight = $this->calculateMatureHeight($child, $currentHeight, $latestRecordDateCopy, $pubertyMonths);

        $heightWhoCurrent = round($heightChangeLasted - $who->height, 2);


        $growthEvaluation = $this->evaluateHeightGrowth($heightChange, $heightChangeWho);

        return [
            'advice_message' => $adviceMessage,
            'oldest_record_exists' => $oldestRecordExists,
            'speed_change' => $heightChange,
            'predicting_adult_height' => $predictingAdultHeight,
            'puberty_months' => $pubertyMonths,
            'height_comparison' => [
                'height_who_current' => $heightWhoCurrent,
                'is_taller_than_who' => $heightWhoCurrent > 0,
                'message' => $growthEvaluation['message'],
            ],
            'child' => new ChildResource($child),
            'is_chart_unlocked' => true,
        ];
    }

    public function calculateMatureHeight($child, $currentHeight, $latestDate, float $pubertyMonths = 0.0): float
    {

        $heightFather = $child->user->father_height ?? 0;
        $heightMother = $child->user->mother_height ?? 0;
        $birthday = $child->birthday;

        // Dậy thì rút ngắn thời gian tăng trưởng: mỗi 12 tháng dậy thì giảm tương đương 1 năm tăng trưởng
        $pubertyYears = round($pubertyMonths / 12);
        $baseAdulthood = ($child->gender == Gender::Male ? 16.0 : 15.0);

        $oneYearBefore = $latestDate->copy()->subYear();


        $oldestRecord = $this->repository->getRecordInDateRange($child->id, $oneYearBefore, $latestDate, true);

        $currentAge = $latestDate->diffInDays($birthday) / 365.3;

        $Adulthood = max($currentAge, $baseAdulthood - $pubertyYears);
        $predictAdulthood = max(0.0, $Adulthood - $currentAge);

        $heightOneYearAgo = $oldestRecord ? $oldestRecord->height : 0;
        $countDays = $latestDate->diffInDays($oldestRecord ? $oldestRecord->assessment_date : $latestDate);
        if ($countDays == 0 || !$oldestRecord) {
            $increasedHeight = $currentHeight - $heightOneYearAgo;
        } else {
            $increasedHeight = ($currentHeight - $heightOneYearAgo) * (365.3 / $countDays);
        }
        $increasedHeight = max(0.0, min(6.5, (float)$increasedHeight));


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

    public function calculateMatureHeightAt19($child, $currentHeight, $latestDate, float $pubertyMonths = 0.0): float
    {
        $birthday = $child->birthday;
        $gender = $child->gender;
        $pubertyYears = round($pubertyMonths / 12);
        $currentAge = round($latestDate->diffInDays($birthday) / 365.3, 1);

        $resultSpeedHeightChange = $this->calculateSpeedHeightChange($currentHeight, $child->id, $latestDate);
        $rawSpeed = (float)$resultSpeedHeightChange['height_change'];
        $increasedHeight = max(0.0, min(6.5, $rawSpeed));

        if ($increasedHeight <= 0) {
            $whoCurrent = $this->getWho(round($currentAge * 12), $gender);
            $rawFallback = $whoCurrent && $whoCurrent->height_change ? (float)$whoCurrent->height_change * 12 : 5.5;
            $effectiveSpeed = max(0.0, min(6.5, $rawFallback));
        } else {
            $effectiveSpeed = $increasedHeight;
        }

        $basePubertyEndAge = ($gender == Gender::Male ? 16.0 : 14.0);
        $pubertyEndAge = $basePubertyEndAge - $pubertyYears;
        $pubertyEndAge = max($currentAge, $pubertyEndAge);

        $startAge = (int)floor($currentAge) + 1;
        if ($startAge < 5) {
            $startAge = 5;
        }
        if ($startAge > 18) {
            $startAge = 18;
        }
        $maxAge = 19;

        $whoHeights = [];
        for ($age = $startAge; $age <= $maxAge + (int)$pubertyYears; $age++) {
            $month = $age * 12;
            $who = $this->getWho($month, $gender);
            $whoHeights[$age] = $who ? (float)$who->height : ($whoHeights[$age - 1] ?? 0.0);
        }

        $prevPredHeight = $currentHeight;
        for ($age = $startAge; $age <= $maxAge; $age++) {
            if ($age < $pubertyEndAge) {
                $predH = $currentHeight + ($age - $currentAge) * $effectiveSpeed;
            } else {
                $shiftedAge = $age + (int)$pubertyYears;
                $whoDelta = 0.2;
                if (isset($whoHeights[$shiftedAge], $whoHeights[$shiftedAge - 1])) {
                    $whoDelta = max(0.0, $whoHeights[$shiftedAge] - $whoHeights[$shiftedAge - 1]);
                } elseif (isset($whoHeights[$age], $whoHeights[$age - 1])) {
                    $whoDelta = max(0.0, $whoHeights[$age] - $whoHeights[$age - 1]);
                }
                $predH = $prevPredHeight + $whoDelta;
            }
            $prevPredHeight = $predH;
        }

        return round($prevPredHeight, 0);
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
        // Làm tròn số tháng dậy thì thành năm: < 6 tháng = 0 năm, >= 6 tháng = 1 năm, >= 18 tháng = 2 năm, v.v.
        // Khớp với cách tính trong Excel: round(tháng / 12)
        $pubertyYears = round($pubertyMonths / 12);

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
        $increasedHeight = max(0.0, min(6.5, $rawSpeed));

        // Nếu tốc độ tăng = 0, fallback theo mức tăng trung bình WHO của lứa tuổi hiện tại
        if ($increasedHeight <= 0) {
            $whoCurrent = $this->getWho(round($currentAge * 12), $gender);
            $rawFallback = $whoCurrent && $whoCurrent->height_change ? (float)$whoCurrent->height_change * 12 : 5.5;
            $effectiveSpeed = max(0.0, min(6.5, $rawFallback));
        } else {
            $effectiveSpeed = $increasedHeight;
        }

        // Tuổi kết thúc dậy thì: Excel formula = basePubertyEndAge - pubertyYears
        // Nam: kết thúc dậy thì mặc định 16 tuổi | Nữ: 14 tuổi
        $basePubertyEndAge = ($gender == Gender::Male ? 16.0 : 14.0);
        $pubertyEndAge = $basePubertyEndAge - $pubertyYears;
        $pubertyEndAge = max($currentAge, $pubertyEndAge);

        // Dự đoán chiều cao trưởng thành theo V1 có tính đến số tháng dậy thì
        $predictedAdultHeight = $this->calculateMatureHeight($child, $currentHeight, $latestRecordDateCopy, $pubertyMonths);

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

        // Pre-load WHO heights cho puberty-shifted delta (khi dậy thì kết thúc sớm N năm,
        // dùng WHO delta ở tuổi+N → cần load thêm data cho tuổi > 19)
        if ($pubertyYears > 0) {
            for ($extraAge = $maxAge + 1; $extraAge <= $maxAge + (int)$pubertyYears; $extraAge++) {
                $who = $this->getWho($extraAge * 12, $gender);
                // Nếu không có WHO data (sau 19 tuổi), dùng chiều cao cuối cùng (tăng trưởng dừng)
                $whoHeights[$extraAge] = $who ? (float)$who->height : ($whoHeights[$extraAge - 1] ?? 0.0);
            }
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
            if ($age < $pubertyEndAge) {
                $predH = $currentHeight + ($age - $currentAge) * $effectiveSpeed;
            } else {
                // Sau tuổi hết dậy thì: dùng WHO delta shifted theo pubertyYears
                // Trẻ dậy thì sớm N năm → đường hậu dậy thì dịch lên N bậc trên bảng WHO
                // VD: PM=12 (1yr) tại tuổi 15 → dùng WHO delta ở tuổi 16 (shift +1)
                $shiftedAge = $age + (int)$pubertyYears;
                $whoDelta = 0.2;
                if (isset($whoHeights[$shiftedAge], $whoHeights[$shiftedAge - 1])) {
                    $whoDelta = max(0.0, $whoHeights[$shiftedAge] - $whoHeights[$shiftedAge - 1]);
                } elseif (isset($whoHeights[$age], $whoHeights[$age - 1])) {
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
        // Đường mục tiêu uốn lượn và tăng trưởng theo hình dạng đường DỰ ĐOÁN:
        // Co giãn tỷ lệ tăng trưởng từ currentHeight -> finalTargetHeight theo tiến trình của đường Dự đoán
        // Điểm đầu: currentHeight (tại currentAge)
        // Điểm cuối: finalTargetHeight (tại maxAge = 19)
        $finalTargetHeight = max($targetHeight, (float)$predictedAdultHeight);
        $predMatureHeight = $prevPredHeight ? round($prevPredHeight, 1) : (float)$predictedAdultHeight;
        $predTotalGrowth = $predMatureHeight - $currentHeight;
        $targetTotalGrowth = $finalTargetHeight - $currentHeight;

        $targetLine = [
            [
                'age' => (float)$currentAge,
                'height' => round($currentHeight, 1),
                'is_current' => true,
            ],
        ];
        for ($age = $startAge; $age <= $maxAge; $age++) {
            $predH = isset($predictionHeights[$age]) ? $predictionHeights[$age] : $prevPredHeight;

            if ($age == $maxAge) {
                $tgtH = $finalTargetHeight;
            } elseif ($predTotalGrowth > 0.01) {
                // Tỷ lệ tiến trình tăng trưởng của đường Dự đoán tại mốc tuổi này (0.0 -> 1.0)
                $predProgress = ($predH - $currentHeight) / $predTotalGrowth;
                $predProgress = min(1.0, max(0.0, $predProgress));
                $tgtH = $currentHeight + $targetTotalGrowth * $predProgress;
            } else {
                $tgtH = $finalTargetHeight;
            }

            // Clamp: đường mục tiêu luôn >= đường dự đoán
            $tgtH = max($tgtH, $predH);

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
            'target_height' => $finalTargetHeight,
            'puberty_months' => $pubertyMonths,
            'prediction_line' => $predictionLine,
            'who_line' => $whoLine,
            'target_line' => $targetLine,
        ];
    }

    /**
     * Dự báo chiều cao V2
     * User nhập Tháng dậy thì bắt buộc trước khi xem kết quả.
     * Response chỉ trả về dự đoán chiều cao trưởng thành, không trả về lời khuyên/so sánh WHO/tốc độ.
     *
     * @param Request $request
     * @return array
     */
    public function indexV2(Request $request): array
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
        $month = round($birthDay->diffInDays($latestDate) / 30.5);

        // Tính sự thay đổi chiều cao (tốc độ tăng trưởng trong năm qua)
        $resultSpeedHeightChange = $this->calculateSpeedHeightChange($currentHeight, $childId, $latestDate);
        $heightChange = $resultSpeedHeightChange['height_change'];
        $oldestRecord = $resultSpeedHeightChange['oldest_record'];
        $oldestRecordExists = (bool)$oldestRecord;

        $heightChangeLasted = $latestRecord ? $latestRecord->height : 0;

        // Lấy thông tin WHO cho độ tuổi và giới tính
        $who = $this->getWho($month, $gender);
        $heightChangeWho = $who ? $who->height_change : 0;

        $pubertyMonths = (float)$data['puberty_months'];
        $predictingAdultHeight = $this->calculateMatureHeightAt19($child, $currentHeight, $latestRecordDateCopy, $pubertyMonths);

        $heightWhoCurrent = $who ? round($heightChangeLasted - $who->height, 2) : 0;
        $growthEvaluation = $this->evaluateHeightGrowth($heightChange, $heightChangeWho);

        return [
            'oldest_record_exists' => $oldestRecordExists,
            'speed_change' => $heightChange,
            'predicting_adult_height' => $predictingAdultHeight,
            'puberty_months' => $pubertyMonths,
            'height_comparison' => [
                'height_who_current' => $heightWhoCurrent,
                'is_taller_than_who' => $heightWhoCurrent > 0,
                'message' => $growthEvaluation['message'],
            ],
            'child' => new ChildResource($child),
            'is_chart_unlocked' => true,
        ];
    }

    /**
     * Biểu đồ chiều cao V2 (2 đường: Dự đoán + WHO)
     * Bỏ hoàn toàn đường Mục tiêu và tham số target_height.
     *
     * @param Request $request
     * @return array
     */
    public function chartV2(Request $request): array
    {
        $data = $request->validated();
        $childId = $data['child_id'];
        $pubertyMonths = (float)$data['puberty_months'];

        // Làm tròn số tháng dậy thì thành năm: < 6 tháng = 0 năm, >= 6 tháng = 1 năm, >= 18 tháng = 2 năm, v.v.
        // Khớp với cách tính trong Excel: round(tháng / 12)
        $pubertyYears = round($pubertyMonths / 12);

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
        $increasedHeight = max(0.0, min(6.5, $rawSpeed));

        // Nếu tốc độ tăng = 0, fallback theo mức tăng trung bình WHO của lứa tuổi hiện tại
        if ($increasedHeight <= 0) {
            $whoCurrent = $this->getWho(round($currentAge * 12), $gender);
            $rawFallback = $whoCurrent && $whoCurrent->height_change ? (float)$whoCurrent->height_change * 12 : 5.5;
            $effectiveSpeed = max(0.0, min(6.5, $rawFallback));
        } else {
            $effectiveSpeed = $increasedHeight;
        }

        // Tuổi kết thúc dậy thì: Excel formula = basePubertyEndAge - pubertyYears
        // Nam: kết thúc dậy thì mặc định 16 tuổi | Nữ: 14 tuổi
        $basePubertyEndAge = ($gender == Gender::Male ? 16.0 : 14.0);
        $pubertyEndAge = $basePubertyEndAge - $pubertyYears;
        $pubertyEndAge = max($currentAge, $pubertyEndAge);

        // Dự đoán chiều cao trưởng thành khớp với mốc 19 tuổi của phác đồ
        $predictedAdultHeight = $this->calculateMatureHeightAt19($child, $currentHeight, $latestRecordDateCopy, $pubertyMonths);

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

        // Pre-load WHO heights cho puberty-shifted delta (khi dậy thì kết thúc sớm N năm,
        // dùng WHO delta ở tuổi+N → cần load thêm data cho tuổi > 19)
        if ($pubertyYears > 0) {
            for ($extraAge = $maxAge + 1; $extraAge <= $maxAge + (int)$pubertyYears; $extraAge++) {
                $who = $this->getWho($extraAge * 12, $gender);
                // Nếu không có WHO data (sau 19 tuổi), dùng chiều cao cuối cùng (tăng trưởng dừng)
                $whoHeights[$extraAge] = $who ? (float)$who->height : ($whoHeights[$extraAge - 1] ?? 0.0);
            }
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
            if ($age < $pubertyEndAge) {
                $predH = $currentHeight + ($age - $currentAge) * $effectiveSpeed;
            } else {
                // Sau tuổi hết dậy thì: dùng WHO delta shifted theo pubertyYears
                // Trẻ dậy thì sớm N năm → đường hậu dậy thì dịch lên N bậc trên bảng WHO
                // VD: PM=12 (1yr) tại tuổi 15 → dùng WHO delta ở tuổi 16 (shift +1)
                $shiftedAge = $age + (int)$pubertyYears;
                $whoDelta = 0.2;
                if (isset($whoHeights[$shiftedAge], $whoHeights[$shiftedAge - 1])) {
                    $whoDelta = max(0.0, $whoHeights[$shiftedAge] - $whoHeights[$shiftedAge - 1]);
                } elseif (isset($whoHeights[$age], $whoHeights[$age - 1])) {
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

        // 3. Tính đường MỤC TIÊU (nếu có target_height)
        $rawTarget = isset($data['target_height']) && (float)$data['target_height'] > 0
            ? (float)$data['target_height']
            : null;

        $targetLine = null;
        $finalTargetHeight = null;

        if ($rawTarget !== null) {
            $predMatureHeight = $prevPredHeight ? round($prevPredHeight, 1) : (float)$predictedAdultHeight;
            // Không cho mục tiêu nhỏ hơn dự đoán: nếu nhỏ hơn thì lấy bằng dự đoán
            $finalTargetHeight = max($rawTarget, (float)$predMatureHeight);
            $predTotalGrowth = $predMatureHeight - $currentHeight;
            $targetTotalGrowth = $finalTargetHeight - $currentHeight;

            $targetLine = [
                [
                    'age' => (float)$currentAge,
                    'height' => round($currentHeight, 1),
                    'is_current' => true,
                ],
            ];
            for ($age = $startAge; $age <= $maxAge; $age++) {
                $predH = isset($predictionHeights[$age]) ? $predictionHeights[$age] : $prevPredHeight;

                if ($age == $maxAge) {
                    $tgtH = $finalTargetHeight;
                } elseif ($predTotalGrowth > 0.01) {
                    // Tỷ lệ tiến trình tăng trưởng của đường Dự đoán tại mốc tuổi này (0.0 -> 1.0)
                    $predProgress = ($predH - $currentHeight) / $predTotalGrowth;
                    $predProgress = min(1.0, max(0.0, $predProgress));
                    $tgtH = $currentHeight + $targetTotalGrowth * $predProgress;
                } else {
                    $tgtH = $finalTargetHeight;
                }

                // Clamp: đường mục tiêu luôn >= đường dự đoán
                $tgtH = max($tgtH, $predH);

                $targetLine[] = [
                    'age' => (float)$age,
                    'height' => round($tgtH, 1),
                    'is_current' => false,
                ];
            }
        }

        $regimenAdvice = $this->buildRegimenAdvice(
            $child,
            $currentAge,
            $currentHeight,
            $latestRecord,
            $predictedAdultHeight,
            $finalTargetHeight
        );

        $res = [
            'child' => new ChildResource($child),
            'current_age' => $currentAge,
            'current_height' => $currentHeight,
            'speed_change' => $rawSpeed,
            'predicted_adult_height' => $predictedAdultHeight,
            'target_height' => $finalTargetHeight,
            'puberty_end_age' => $pubertyEndAge,
            'puberty_months' => $pubertyMonths,
            'prediction_line' => $predictionLine,
            'who_line' => $whoLine,
            'target_line' => $targetLine,
            'regimen_advice' => $regimenAdvice,
        ];

        return $res;
    }

    /**
     * Xây dựng nội dung và công thức lời khuyên phác đồ phát triển chiều cao (theo chuẩn file Loi nhan phat do.docx)
     */
    protected function buildRegimenAdvice($child, float $currentAge, float $currentHeight, $latestRecord, ?float $predictedAdultHeight, ?float $finalTargetHeight): array
    {
        $childName = $child->name ?? ($child->fullname ?? 'bé');
        $gender = $child->gender;
        $genderVal = $gender instanceof Gender ? $gender->value : $gender;

        // 1. Tính BMI hiện tại từ bản ghi mới nhất
        $currentWeight = $latestRecord && $latestRecord->weight ? (float)$latestRecord->weight : 0.0;
        $currentBmi = 0.0;
        if ($latestRecord && $latestRecord->bmi) {
            $currentBmi = (float)$latestRecord->bmi;
        } elseif ($currentHeight > 0 && $currentWeight > 0) {
            $currentBmi = round($currentWeight / pow($currentHeight / 100, 2), 1);
        }

        // 2. Tuổi tính toán tham chiếu bảng WHO BMI
        $childAgeYears = (int)round($currentAge);
        if ($childAgeYears < 1) $childAgeYears = 1;
        if ($childAgeYears > 19) $childAgeYears = 19;

        $bmiStandard = Bmi::where('age', $childAgeYears)
            ->where('gender', $genderVal)
            ->where('status', ActiveStatus::Active)
            ->first();

        if (!$bmiStandard) {
            $bmiStandard = Bmi::where('age', $childAgeYears)
                ->where('gender', $genderVal)
                ->first();
        }

        $bmiWho = $bmiStandard ? (float)$bmiStandard->z_score_0 : null;
        $bmiAssessment = 'Đạt chuẩn';
        $bmiAdvice = 'BMI ở mức trung bình so với bé cùng tuổi và giới tính. Tiếp tục duy trì chế độ ăn đa dạng, vận động phù hợp và thói quen sinh hoạt lành mạnh.';

        if ($bmiStandard && $currentBmi > 0) {
            if ($currentBmi <= (float)$bmiStandard->z_score_minus_3) {
                $bmiAssessment = 'Suy dinh dưỡng';
                $bmiAdvice = 'Bé có nguy cơ thiếu dinh dưỡng, cần chú ý chế độ ăn, sức khỏe và theo dõi tăng trưởng. Nếu tình trạng kéo dài, nên tham khảo bác sĩ hoặc chuyên gia dinh dưỡng..';
            } elseif ($currentBmi <= (float)$bmiStandard->z_score_minus_2) {
                $bmiAssessment = 'Quá gầy';
                $bmiAdvice = 'Cha mẹ nên đảm bảo bé được ăn đủ chất, đa dạng thực phẩm và theo dõi chiều cao, cân nặng định kỳ.';
            } elseif ($currentBmi <= (float)$bmiStandard->z_score_minus_1) {
                $bmiAssessment = 'Hơi gầy';
                $bmiAdvice = 'BMI hơi thấp nhưng vẫn có thể phù hợp với quá trình phát triển của bé. Duy trì chế độ ăn cân đối, vận động và theo dõi tăng trưởng thường xuyên.';
            } elseif ($currentBmi <= (float)$bmiStandard->z_score_plus_1) {
                $bmiAssessment = 'Đạt chuẩn';
                $bmiAdvice = 'BMI ở mức trung bình so với bé cùng tuổi và giới tính. Tiếp tục duy trì chế độ ăn đa dạng, vận động phù hợp và thói quen sinh hoạt lành mạnh.';
            } elseif ($currentBmi <= (float)$bmiStandard->z_score_plus_2) {
                $bmiAssessment = 'Hơi béo';
                $bmiAdvice = 'BMI hơi cao so với mức trung bình. Nên chú ý chất lượng khẩu phần, hạn chế thực phẩm nhiều đường và tăng cường vận động, đồng thời tiếp tục theo dõi tăng trưởng.';
            } elseif ($currentBmi <= (float)$bmiStandard->z_score_plus_3) {
                $bmiAssessment = 'Tương đối Béo';
                $bmiAdvice = 'BMI cao. Bé có nguy cơ thừa cân/béo phì tùy theo độ tuổi. Nên điều chỉnh thói quen ăn uống, tăng vận động và tham khảo chuyên gia nếu cần.';
            } else {
                $bmiAssessment = 'Béo phì';
                $bmiAdvice = 'BMI rất cao. Bé có nguy cơ béo phì cao. Nên được bác sĩ hoặc chuyên gia dinh dưỡng đánh giá để có hướng theo dõi và điều chỉnh phù hợp.';
            }
        }

        $displayTarget = $finalTargetHeight ? round($finalTargetHeight) : null;
        $displayPred = $predictedAdultHeight ? round($predictedAdultHeight) : null;

        return [
            'child_name' => $childName,
            'predicted_adult_height' => $displayPred,
            'target_height' => $displayTarget,
            'bmi' => $currentBmi > 0 ? $currentBmi : null,
            'bmi_who' => $bmiWho,
            'bmi_assessment' => $bmiAssessment,
            'bmi_advice' => $bmiAdvice,
            'intro' => "Lộ trình phát triển chiều cao của bạn {$childName} từ hiện tại đến tuổi 19, giúp bố mẹ theo dõi tiến trình và chủ động điều chỉnh mục tiêu theo từng giai đoạn.",
            'prediction_note' => "Theo dữ liệu hiện tại, chiều cao dự đoán của bạn {$childName} khi trưởng thành khoảng {$displayPred} cm (+/-5).\n\nĐây là giá trị dự đoán, được tính dựa trên tốc độ tăng trưởng, yếu tố di truyền và tình trạng phát triển hiện tại của bé. Kết quả có thể thay đổi theo dinh dưỡng, vận động, giấc ngủ và sinh hoạt trong những năm tiếp theo.\n\nBố mẹ nên cập nhật chiều cao của bé định kỳ, tốt nhất vào đầu mỗi tháng. CHĂM CON 360 sẽ tự động cập nhật lại dự đoán theo dữ liệu mới nhất.",
            'target_note' => $displayTarget ? "Đây là mục tiêu chiều cao trưởng thành {$displayTarget} cm mà bố mẹ đặt cho bé. Từ mục tiêu này, CHĂM CON 360 phân bổ thành các cột mốc theo từng năm để bố mẹ và bé dễ theo dõi và phấn đấu.\n\nMục tiêu không cố định. Nếu bé phát triển tốt hơn dự kiến, bố mẹ có thể điều chỉnh mục tiêu cao hơn; nếu tốc độ tăng trưởng chậm lại, có thể điều chỉnh cho phù hợp." : null,
            'target_header' => $displayTarget ? "Để đạt mục tiêu {$displayTarget} cm bé phải tập trung vào 3 việc: ăn đủ – vận động đều – sinh hoạt đúng:" : "Để phát huy tối đa tiềm năng chiều cao, bé cần tập trung vào 3 việc: ăn đủ – vận động đều – sinh hoạt đúng:",
            'target_actions' => [
                [
                    'title' => '1. Ăn đủ và đa dạng',
                    'content' => 'Cho con ăn đa dạng, đủ đạm từ thịt, cá, trứng, đậu; bổ sung thêm các thực phẩm giàu canxi như sữa. Kết hợp các loại rau xanh cung cấp đầy đủ các loại Vitamin (D3, K2) và khoáng chất (kẽm, Magie). Không dùng nước ngọt, bánh kẹo và đồ ăn quá nhiều đường.',
                ],
                [
                    'title' => '2. Vận động đều đặn',
                    'content' => 'Khuyến khích bé vận động khoảng 60 phút mỗi ngày, phù hợp với độ tuổi và thể trạng. Có thể lựa chọn nhảy dây, bóng rổ, cầu lông, bơi lội và các bài tập bổ trợ chiều cao trong CHĂM CON 360. Duy trì đều đặn quan trọng hơn tập một cách quá sức.',
                ],
                [
                    'title' => '3. Ngủ đủ và sinh hoạt lành mạnh',
                    'content' => 'Khuyến khích bé ngủ sớm trước 22 giờ, ngủ đủ theo độ tuổi 8-10 tiếng/ngày, vui chơi ngoài trời và duy trì tinh thần vui vẻ, thoải mái.',
                ],
            ],
            'bmi_general_note' => 'Trẻ béo hay gầy không chỉ dựa vào cân nặng để đánh giá mà phải dùng chỉ số BMI để đánh giá trẻ đang gầy, đạt chuẩn hay có xu hướng thừa cân. Bé cần duy trì chỉ số BMI hợp lý để giúp chiều cao phát triển tốt hơn.',
        ];
    }
}
