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

class RatingPQResource extends JsonResource
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
        $isContentVisible = $this->checkUserPackage($this->assessment_date);
        return [
            'id' => $this->id,
            'assessment_date' => format_date($this->assessment_date),
            'height' => $this->height,
            'weight' => $this->weight,
            'strength' => $this->strength,
            'endurance' => $this->endurance,
            'bmi' => $this->bmi,
            'score' => $this->score,
            'bmi_result' => $this->bmi_result,
            'height_result' => $this->height_result,
            'height_change' => round($this->height_change, 2),
            'weight_change' => $this->weight_change !== null ? round((float)$this->weight_change, 2) : null,
            'child' => new ChildResource($this->child),
            'checked' => $isContentVisible

        ];
    }

}
