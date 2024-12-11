<?php

namespace App\Admin\Http\Requests\Expected;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class ExpectedRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'age' => ['required', 'integer'],
            'height_expected' => ['required', 'numeric'],
            'weight_expected' => ['required', 'numeric'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'integer', 'exists:expecteds,id'],
            'age' => ['required', 'integer'],
            'height_expected' => ['required', 'numeric'],
            'weight_expected' => ['required', 'numeric'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    public function messages()
    {
        return [
            'age.required' => 'Vui lòng nhập tuổi',
            'age.integer' => 'Tuổi phải là số nguyên',
            'height_expected.required' => 'Vui lòng nhập chiều cao dự kiến',
            'height_expected.numeric' => 'Chiều cao dự kiến phải là số',
            'weight_expected.required' => 'Vui lòng nhập cân nặng dự kiến',
            'weight_expected.numeric' => 'Cân nặng dự kiến phải là số',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}