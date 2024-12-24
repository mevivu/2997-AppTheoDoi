<?php

namespace App\Admin\Http\Requests\Support;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Support\SupportType;
use Illuminate\Validation\Rules\Enum;

class SupportRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'title' => ['required'],
            'content' => ['required'],
            'type' => ['required', new Enum(SupportType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\Support,id'],
            'title' => ['required'],
            'content' => ['required'],
            'type' => ['required', new Enum(SupportType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề',
            'content.required' => 'Vui lòng nhập nội dung',
            'type.required' => 'Vui lòng chọn loại hỗ trợ',
            'status.required' => 'Vui lòng chọn trạng thái',
            'id.required' => 'Vui lòng chọn hỗ trợ cần sửa',
            'id.exists' => 'Hỗ trợ không tồn tại',
        ];
    }
}