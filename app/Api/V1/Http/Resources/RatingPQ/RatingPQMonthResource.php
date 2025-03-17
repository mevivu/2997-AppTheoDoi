<?php

namespace App\Api\V1\Http\Resources\RatingPQ;

use App\Api\V1\Http\Resources\Child\ChildResource;
use App\Api\V1\Support\CheckPackage;
use App\Enums\User\Gender;
use App\Models\Bmi;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RatingPQMonthResource extends JsonResource
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
        $age = $child->age;
        $gender = $child->gender;
        $bmiInfo = Bmi::where('age', $age)
            ->where('gender', $gender)
            ->first();
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
            'bmi_info' => [
                'z_score_0' => $bmiInfo ? $bmiInfo->z_score_0 : null,

            ]


        ];
    }


}
