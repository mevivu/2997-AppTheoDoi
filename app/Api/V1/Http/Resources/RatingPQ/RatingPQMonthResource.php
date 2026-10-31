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
        $formatChartNum = static function ($val): ?string {
            if ($val === null || (float)$val <= 0) {
                return null;
            }
            $num = round((float)$val, 1);
            return floor($num) == $num ? (string)(int)$num : (string)$num;
        };

        return [
            'id' => $this->id,
            'assessment_date' => format_date($this->assessment_date),
            'month' => $this->assessment_date ? (int)$this->assessment_date->format('n') : null,
            'height' => $this->height !== null ? (int)round((float)$this->height) : null,
            'weight' => $this->weight !== null ? round((float)$this->weight, 1) : 0,
            'strength' => $this->strength !== null ? (int)round((float)$this->strength) : null,
            'endurance' => $this->endurance !== null ? (int)round((float)$this->endurance) : null,
            'bmi' => $this->bmi !== null ? round((float)$this->bmi, 1) : null,
            'bmi_result' => $this->bmi_result,
            'height_result' => $this->height_result,
            'height_change' => round((float)($this->height_change ?? 0), 2),
            'weight_change' => $this->weight_change !== null ? round((float)$this->weight_change, 2) : null,
            'bmi_info' => [
                'z_score_0' => $bmiInfo ? $bmiInfo->z_score_0 : null,
            ],
            'chart_labels' => [
                'height' => $formatChartNum($this->height),
                'weight' => $formatChartNum($this->weight),
                'strength' => $formatChartNum($this->strength),
                'endurance' => $formatChartNum($this->endurance),
                'bmi' => $formatChartNum($this->bmi),
            ],
        ];
    }


}
