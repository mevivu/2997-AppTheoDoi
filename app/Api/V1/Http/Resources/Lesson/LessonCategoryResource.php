<?php

namespace App\Api\V1\Http\Resources\Lesson;

use App\Enums\Lesson\EducationPillar;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class LessonCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $pillarValue = $this->pillar instanceof EducationPillar ? $this->pillar->value : (string) $this->pillar;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'description' => $this->description,
            'pillar' => $pillarValue,
            'pillar_label' => $this->pillar_label,
            'age_group_id' => $this->age_group_id,
            'age_group_name' => $this->ageGroup?->name,
            'sort_order' => (int) $this->sort_order,
            'lessons_count' => (int) ($this->lessons_count ?? 0),
            'free_lessons_count' => (int) ($this->free_lessons_count ?? 0),
            'vip_lessons_count' => (int) ($this->vip_lessons_count ?? 0),
        ];
    }
}
