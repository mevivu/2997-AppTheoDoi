<?php

namespace App\Api\V1\Http\Resources\Exercise;

use App\Enums\Exercise\ExerciseDifficulty;
use App\Enums\Video\VideoAccessType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ExerciseEducationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $isLocked = (bool) ($this->is_locked ?? false);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'content' => $isLocked ? null : $this->content,
            'exercise_category_id' => $this->exercise_category_id,
            'category_name' => $this->category?->name,
            'difficulty' => $this->difficulty?->value,
            'difficulty_name' => $this->difficulty ? ExerciseDifficulty::getDescription($this->difficulty->value) : null,
            'frequency' => $this->frequency,
            'benefit' => $this->benefit,
            'tools' => $this->tools,
            'practice_count' => (int) $this->practice_count,
            'access_type' => $this->access_type?->value,
            'access_type_label' => $this->access_type ? VideoAccessType::getDescription($this->access_type->value) : null,
            'is_locked' => $isLocked,
            'media' => $isLocked ? [] : ExerciseMediaResource::collection($this->whenLoaded('media')),
            'sort_order' => (int) $this->sort_order,
            'created_at' => format_datetime($this->created_at),
        ];
    }
}
