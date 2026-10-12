<?php

namespace App\Api\V1\Http\Resources\Journal;

use App\Api\V1\Support\CheckPackage;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class JournalResource extends JsonResource
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
        $isContentVisible = $this->checkUserPackage($this->created_at);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'content' => $this->content,
            'image' => $this->image ? json_decode($this->image) : null,
            'created_at' => format_date($this->created_at),
            'checked' => $isContentVisible,
            'milestone_id' => $this->milestone_id,
            'milestone' => $this->milestone ? [
                'id' => $this->milestone->id,
                'name' => $this->milestone->name,
                'slug' => $this->milestone->slug,
                'stage' => $this->milestone->stage instanceof \BackedEnum ? $this->milestone->stage->value : (string)$this->milestone->stage,
            ] : null,
        ];
    }
}
