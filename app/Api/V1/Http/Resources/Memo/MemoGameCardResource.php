<?php

namespace App\Api\V1\Http\Resources\Memo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemoGameCardResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this['id'] ?? null,
            'key' => $this['key'] ?? null,
            'name' => $this['name'] ?? '',
            'image' => $this['image'] ?? null,
            'icon' => $this['icon'] ?? null,
            'color' => $this['color'] ?? null,
            'audio' => $this['audio'] ?? null,
            'is_virtual' => (bool) ($this['is_virtual'] ?? false),
        ];
    }
}
