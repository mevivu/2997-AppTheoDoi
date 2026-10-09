<?php

namespace App\Api\V1\Http\Resources\Introduction;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class IntroductionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $plainExcerpt = '';
        if (!empty($this->excerpt)) {
            $formatted = preg_replace('/<(?:br|\/p|\/div|\/li|h[1-6]|\/h[1-6])\s*\/?>/i', ' ', (string) $this->excerpt);
            $plainExcerpt = preg_replace('/\s+/', ' ', trim(html_entity_decode(strip_tags($formatted), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'section_type' => $this->section_type?->value ?? $this->section_type,
            'section_name' => $this->section_type?->label() ?? '',
            'image' => formatImageUrl($this->image),
            'icon' => $this->icon,
            'excerpt' => $this->excerpt,
            'plain_excerpt' => $plainExcerpt,
            'content' => $this->content,
            'sort_order' => $this->sort_order,
            'created_at' => format_datetime($this->created_at),
        ];
    }
}
