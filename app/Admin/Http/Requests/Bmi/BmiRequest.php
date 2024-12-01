<?php

namespace App\Admin\Http\Requests\Bmi;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class BmiRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'age' => ['required', 'integer'],
            'bmi' => ['required', 'numeric'],
            'gender' => ['required', new Enum(Gender::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'integer', 'exists:App\Models\Bmi,id'],
            'age' => ['required', 'integer'],
            'bmi' => ['required', 'numeric'],
            'gender' => ['required', new Enum(Gender::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    public function messages()
    {
        return [
            'age.required' => 'Vui lòng nhập tuổi',
            'age.integer' => 'Tuổi phải là số nguyên',
            'bmi.required' => 'Vui lòng nhập chỉ số BMI',
            'bmi.numeric' => 'Chỉ số BMI phải là số',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}