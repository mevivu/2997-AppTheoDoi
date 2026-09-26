<?php

namespace App\Api\V1\Http\Resources\Exercise;

use App\Enums\Exercise\ExerciseTopic;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ExerciseCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'topic' => $this->topic?->value,
            'topic_name' => $this->topic ? ExerciseTopic::getDescription($this->topic->value) : null,
            'age_group_id' => $this->age_group_id,
            'age_group_name' => $this->ageGroup?->name,
            'icon' => $this->icon,
            'sort_order' => (int) $this->sort_order,
            'exercises_count' => $this->whenCounted('exercises'),
        ];
    }
}
