<?php

namespace App\Api\V1\Http\Requests\Video;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Video\VideoAccessType;
use Illuminate\Validation\Rules\Enum;

class VideoListRequest extends BaseRequest
{
    /**
     * Quy tắc validation khi gọi method GET
     */
    protected function methodGet(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:video_categories,id'],
            'video_category_id' => ['nullable', 'integer', 'exists:video_categories,id'],
            'age_group_id' => ['nullable', 'integer', 'exists:age_groups,id'],
            'access_type' => ['nullable', 'string', new Enum(VideoAccessType::class)],
            'keyword' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Danh mục video không tồn tại.',
            'video_category_id.exists' => 'Danh mục video không tồn tại.',
            'age_group_id.exists' => 'Nhóm độ tuổi không tồn tại.',
            'access_type.enum' => 'Loại phân quyền truy cập không hợp lệ (chỉ chấp nhận free hoặc vip).',
            'keyword.max' => 'Từ khóa tìm kiếm không được vượt quá 100 ký tự.',
            'page.min' => 'Số trang phải lớn hơn hoặc bằng 1.',
            'limit.min' => 'Số lượng trên trang phải từ 1 đến 50.',
            'limit.max' => 'Số lượng trên trang không được vượt quá 50.',
        ];
    }
}
