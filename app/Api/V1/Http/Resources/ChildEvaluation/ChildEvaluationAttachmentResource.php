<?php

namespace App\Api\V1\Http\Resources\ChildEvaluation;

use Illuminate\Http\Resources\Json\JsonResource;

class ChildEvaluationAttachmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size_bytes' => $this->size_bytes,
            'width' => $this->width,
            'height' => $this->height,
            'sort_order' => $this->sort_order,
            'created_at' => format_datetime($this->created_at),
        ];
    }
}
