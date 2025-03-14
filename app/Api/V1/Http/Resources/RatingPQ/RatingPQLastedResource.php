<?php

namespace App\Api\V1\Http\Resources\RatingPQ;

use App\Api\V1\Http\Resources\Child\ChildResource;
use App\Api\V1\Support\CheckPackage;
use App\Enums\User\Gender;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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
        $child = $this->child;
        $heightMature = $this->calculateMatureHeight($child);
        return [
            'id' => $this->id,
            'assessment_date' => format_date($this->assessment_date),
            'height' => $this->height,
            'weight' => $this->weight,
            'strength' => $this->strength,
            'endurance' => $this->endurance,
            'bmi' => $this->bmi,
            'bmi_result' => $this->bmi_result,
            'height_result' => $this->height_result,
            'height_change' => round($this->height_change, 2),
            'height_mature' =>$heightMature,


        ];
    }
    public function calculateMatureHeight($child): float
    {
        $heightFather = $child->user->father_height;
        $heightMother = $child->user->mother_height;

        $predictedHeightMale = ($heightFather + $heightMother + 13) / 2 + 5;
        $predictedHeightFemale = ($heightMother + $heightMother - 13) / 2 + 3;

        if ($child->gender == Gender::Male) {
            return $predictedHeightMale * 0.3 + $this->height * 0.7;
        } else {
            return $predictedHeightFemale * 0.3 + $this->height * 0.7;
        }
    }


}
