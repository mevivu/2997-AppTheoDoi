<?php

namespace App\Admin\Http\Requests\QuestionGroup;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Group\GroupType;
use Illuminate\Validation\Rules\Enum;

class QuestionGroupRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required'],
            'description' => ['nullable'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'type' => ['required', new Enum(GroupType::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:App\Models\QuestionGroup,id'],
            'name' => ['required'],
            'description' => ['nullable'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'type' => ['required', new Enum(GroupType::class)],
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
