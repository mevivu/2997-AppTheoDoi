<?php

namespace App\Admin\Http\Resources\Memo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemoPlayCardResource extends JsonResource
{
    /**
     * Không bọc data key
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
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
