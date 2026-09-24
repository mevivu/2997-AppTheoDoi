<?php

namespace App\Api\V1\Http\Resources\RatingPQ;

use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Repositories\WeightHeightWho\WhoRepositoryInterface;
use App\Api\V1\Support\CheckPackage;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use JsonSerializable;

class RatingPQLastedResource extends JsonResource
{
    use CheckPackage;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Exception
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $repository = app(RatingPQRepositoryInterface::class);

        $child = $this->child;
        $month = $child->month;
        $gender = $child->gender;
        $who = $this->getWho($month, $gender);
        $height = $who->height ?? 0;

        $latestRecord = $repository->getQueryBuilder()->where('child_id', $child->id)
            ->latest('assessment_date')
            ->first();
        $currentHeight = $latestRecord ? $latestRecord->height : 0;
        $latestDate = $latestRecord ? $latestRecord->assessment_date : Carbon::now();
        $heightService = app(\App\Api\V1\Services\HeightPrediction\HeightPredictionServiceInterface::class);
        $predictingAdultHeight = $heightService->calculateMatureHeightAt19($child, $currentHeight, $latestDate);
        $who228 = $this->getWho(228, $gender);
        $whoAdultHeight = $who228 && $who228->height > 0 ? $who228->height : ($gender == Gender::Male ? 176.5 : 163.0);
        $heightMature = $this->mapHeightDeltaToScore((float)$predictingAdultHeight, (float)$whoAdultHeight);
        $heightScore = $height > 0 ? $this->mapHeightDeltaToScore((float)$this->height, (float)$height) : 1;

        return [
            'id' => $this->id,
            'assessment_date' => format_date($this->assessment_date),
            'height' => $heightScore,
            'weight' => min(10, round($this->weight / 0.1, 1)),
            'strength' => min(10, round($this->strength / 0.1, 1)),
            'endurance' => min(10, round($this->endurance / 0.1, 1)),
            'bmi' => min(10, round($this->bmi / 0.1, 1)),
            'bmi_result' => $this->bmi_result,
            'height_result' => $this->height_result,
            'height_change' => round($this->height_change, 2),
            'weight_change' => $this->weight_change !== null ? round((float)$this->weight_change, 2) : null,
            'height_mature' => $heightMature,
        ];
    }

    public function getWho($month, $gender)
    {
        $repository = app(WhoRepositoryInterface::class);
        return $repository->getBy(
            [
                'month' => $month,
                'gender' => $gender,
                'status' => ActiveStatus::Active,
            ]
        )->first();
    }

    public function calculateMatureHeight($child, $currentHeight, $latestDate): float
    {
        $repository = app(RatingPQRepositoryInterface::class);
        $heightFather = $child->user->father_height ?? 0;
        $heightMother = $child->user->mother_height ?? 0;
        $currentAge = $child->age;
        $Adulthood = $child->gender == Gender::Male ? 16 : 15;
        $predictAdulthood = $Adulthood - $currentAge;

        $oneYearBefore = $latestDate->subYear();

        $oldestRecord = $repository->getQueryBuilder()
            ->where('child_id', $child->id)
            ->whereBetween('assessment_date', [$oneYearBefore, $latestDate])
            ->oldest('assessment_date')
            ->first();
        $heightOneYearAgo = $oldestRecord ? $oldestRecord->height : 0;
        $increasedHeight = $currentHeight - $heightOneYearAgo;
        $increasedHeight = max(0.0, min(6.5, (float)$increasedHeight));
        $adultHeightPrediction = $predictAdulthood * $increasedHeight;


        $predictedHeightMale = ($heightFather + $heightMother + 13) / 2 + 5;
        $predictedHeightFemale = ($heightFather + $heightMother - 13) / 2 + 3;
        if ($child->age > 5) {
            $CurrentHeightAttainmentForecast = $currentHeight + $adultHeightPrediction;
        } else {
            $ageCheckMonth = $child->gender == Gender::Male ? 24 : 18;
            $ratingPq = $repository->getQueryBuilder()
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

        return $CurrentHeightAttainmentForecast;
    }

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
}
