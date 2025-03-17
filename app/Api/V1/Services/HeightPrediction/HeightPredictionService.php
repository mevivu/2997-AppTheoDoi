<?php

namespace App\Api\V1\Services\HeightPrediction;


use App\Api\V1\Http\Resources\Child\ChildResource;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Repositories\WeightHeightWho\WhoRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Enums\ActiveStatus;
use App\Enums\Package\PackageStatus;
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
        $latestRecord = $this->repository->getQueryBuilder()->where('child_id', $childId)
            ->latest('assessment_date')
            ->first();

        $currentHeight = $latestRecord ? $latestRecord->height : 0;

        // Lấy ngày đánh giá mới nhất hoặc ngày hiện tại nếu không có bản ghi
        $latestDate = $latestRecord ? $latestRecord->assessment_date : Carbon::now();

        // Tính sự thay đổi chiều cao
        $heightChange = $this->calculateSpeedHeightChange($currentHeight, $childId, $latestDate);
        $heightChangeLasted = $latestRecord ? $latestRecord->height_change : 0;

        // Tính tháng từ ngày sinh đến latestDate
        $month = floor($birthDay->diffInDays($latestDate) / 30.5);

        // Lấy thông tin WHO cho độ tuổi và giới tính
        $who = $this->getWho($month, $gender);
        $heightWho = $who->height;

        $adviceMessage = $this->getAdviceMessage($currentHeight, $heightWho);
        $predictingAdultHeight = $this->calculateMatureHeight($child, $currentHeight);
        $heightWhoCurrent = abs($heightChangeLasted - $who->height );

        return [
            'advice_message' => $adviceMessage,
            'speed_change' => $heightChange,
            'predicting_adult_height' => $predictingAdultHeight,
            'height_comparison' => [
                'height_who_current' => $heightWhoCurrent,
                'is_taller_than_who' => $heightChangeLasted > $who->height,
            ],
            'child' => new ChildResource($child)
        ];
    }

    public function calculateMatureHeight($child, $currentHeight): float
    {
        $heightFather = $child->user->father_height;
        $heightMother = $child->user->mother_height;

        $predictedHeightMale = ($heightFather + $heightMother + 13) / 2 + 5;
        $predictedHeightFemale = ($heightMother + $heightMother - 13) / 2 + 3;

        if ($child->gender == Gender::Male) {
            return $predictedHeightMale * 0.3 + $currentHeight * 0.7;
        } else {
            return $predictedHeightFemale * 0.3 + $currentHeight * 0.7;
        }
    }


    public function calculateSpeedHeightChange($currentHeight, $childId, $latestDate): int
    {
        $oneYearBefore = $latestDate->subYear();

        $oldestRecord = $this->repository->getQueryBuilder()
            ->where('child_id', $childId)
            ->whereBetween('assessment_date', [$oneYearBefore, $latestDate])
            ->oldest('assessment_date')
            ->first();

        return abs($currentHeight - ($oldestRecord ? $oldestRecord->height : 0));
    }


    public function getAdviceMessage($currentHeight, $heightWho): string
    {
        if ($currentHeight >= $heightWho) {
            return "Bố mẹ cần thay đổi chế độ dinh dưỡng và vận động cho con hoặc tốt nhất là đi khám bác sĩ dinh dưỡng.";
        } else {
            return "Bố mẹ nên duy trì hoặc làm tốt hơn chế độ dinh dưỡng và vận động cho con như hiện tại.";
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
