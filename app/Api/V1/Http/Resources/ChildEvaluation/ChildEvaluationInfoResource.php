<?php

namespace App\Api\V1\Http\Resources\ChildEvaluation;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ChildEvaluationInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Exception
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'class' => [
                'id' => $this->resource['class']->id,
                'name' => $this->resource['class']->name
            ],
            'subjects' => $this->resource['subjects']->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                ];
            }),
            'qualities' => $this->resource['qualities']->map(function ($quality) {
                return [
                    'id' => $quality->id,
                    'name' => $quality->name,
                ];
            }),
            'capabilities' => $this->resource['capabilities']->map(function ($capability) {
                return [
                    'id' => $capability->id,
                    'name' => $capability->name,
                ];
            }),
        ];
    }


}
