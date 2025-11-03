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


        return [
            'advice_message' => $adviceMessage,
            'oldest_record_exists' => $oldestRecordExists,
            'speed_change' => $heightChange,
            'predicting_adult_height' => $predictingAdultHeight,
            'height_comparison' => [
                'height_who_current' => $heightWhoCurrent,
                'is_taller_than_who' => $heightChangeLasted > $who->height,
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
        $increasedHeight = $currentHeight - $heightOneYearAgo;
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
        $result = $heightChangeWho * 0.75 * 12;
        if ($heightChange < $result) {
            return "Bố mẹ cần thay đổi chế độ dinh dưỡng và vận động cho con hoặc tốt nhất là đi khám bác sĩ dinh dưỡng.";
        } else {
            return "Bố mẹ nên duy trì hoặc làm tốt hơn chế độ dinh dưỡng và vận động cho con như hiện tại.";
        }
    }

    public function calculateCurrentHeightChangeWithHeightChanWho($heightChange, $heightChangeWho): bool
    {
        $result1 = $heightChangeWho * 0.75 * 12;
        $result2 = $heightChangeWho * 1.25 * 12;
        if ($heightChange < $result1) {
            return false;
        } else {
            return true;
        }
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


}
