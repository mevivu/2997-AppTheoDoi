<?php

namespace App\Admin\Http\Requests\Memo;

use App\Api\V1\Http\Requests\BaseRequest;

class MemoCardBulkRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'memo_theme_id' => ['required', 'integer', 'exists:memo_themes,id'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'memo_theme_id.required' => 'Vui lòng chọn chủ đề để nạp thẻ',
            'images.required' => 'Vui lòng chọn ít nhất 1 hình ảnh thẻ bài',
            'images.min' => 'Vui lòng chọn ít nhất 1 hình ảnh',
            'images.*.image' => 'File tải lên phải là hình ảnh hợp lệ (JPG, PNG, WEBP)',
        ];
    }
}
