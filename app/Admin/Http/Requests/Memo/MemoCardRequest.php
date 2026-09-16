<?php

namespace App\Admin\Http\Requests\Memo;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;

class MemoCardRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'memo_theme_id' => ['required', 'integer', 'exists:memo_themes,id'],
            'name' => ['required', 'string', 'max:191'],
            'image' => ['nullable'],
            'audio' => ['nullable'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:memo_cards,id'],
            'memo_theme_id' => ['required', 'integer', 'exists:memo_themes,id'],
            'name' => ['required', 'string', 'max:191'],
            'image' => ['nullable'],
            'audio' => ['nullable'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'memo_theme_id.required' => 'Vui lòng chọn chủ đề cho thẻ',
            'memo_theme_id.exists' => 'Chủ đề đã chọn không tồn tại',
            'name.required' => 'Vui lòng nhập tên thẻ bài',
            'image.required' => 'Vui lòng upload hình ảnh mặt trước thẻ',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
