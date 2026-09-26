<?php

namespace App\Admin\Http\Requests\AgeGroup;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;

class AgeGroupRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'min_months' => ['nullable', 'integer', 'min:0'],
            'max_months' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\AgeGroup,id'],
            'name' => ['required', 'string', 'max:100'],
            'min_months' => ['nullable', 'integer', 'min:0'],
            'max_months' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên nhóm tuổi',
            'name.max' => 'Tên nhóm tuổi không được vượt quá 100 ký tự',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
