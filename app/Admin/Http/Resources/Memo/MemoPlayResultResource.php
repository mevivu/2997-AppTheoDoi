<?php

namespace App\Admin\Http\Resources\Memo;

use App\Traits\RouteAdminSystem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemoPlayResultResource extends JsonResource
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
            'success' => true,
            'message' => 'Đã lưu kết quả bài test vào hệ thống thành công!',
            'rating_id' => $this->id,
            'score' => (float) $this->score,
            'evaluation_label' => $this->evaluation_label,
            'feedback' => $this->feedback,
            'view_url' => route(RouteAdminSystem::MEMO_RATING_SHOW, $this->id),
        ];
    }
}
