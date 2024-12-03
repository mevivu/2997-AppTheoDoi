<?php

namespace App\Admin\Http\Requests\QuestionGroup;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;

class QuestionGroupRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'name' => ['required'],
            'description' => ['nullable'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'integer', 'exists:App\Models\QuestionGroup,id'],
            'name' => ['required'],
            'description' => ['nullable'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên nhóm câu hỏi',
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.enum' => 'Trạng thái không hợp lệ',
            'id.required' => 'ID không hợp lệ',
            'id.integer' => 'ID không hợp lệ',
        ];
    }
}