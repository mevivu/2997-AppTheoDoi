<?php

namespace App\Admin\Http\Requests\Bmi;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class BmiRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'age' => ['required', 'integer'],
            'gender' => ['required', new Enum(Gender::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'z_score_minus_3' => ['required', 'numeric'],
            'z_score_minus_2' => ['required', 'numeric'],
            'z_score_minus_1' => ['required', 'numeric'],
            'z_score_0' => ['required', 'numeric'],
            'z_score_plus_1' => ['required', 'numeric'],
            'z_score_plus_2' => ['required', 'numeric'],
            'z_score_plus_3' => ['required', 'numeric'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:App\Models\Bmi,id'],
            'age' => ['required', 'integer'],
            'gender' => ['required', new Enum(Gender::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'z_score_minus_3' => ['required', 'numeric'],
            'z_score_minus_2' => ['required', 'numeric'],
            'z_score_minus_1' => ['required', 'numeric'],
            'z_score_0' => ['required', 'numeric'],
            'z_score_plus_1' => ['required', 'numeric'],
            'z_score_plus_2' => ['required', 'numeric'],
            'z_score_plus_3' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'age.required' => 'Vui lòng nhập tuổi',
            'age.integer' => 'Tuổi phải là số nguyên',
            'z_score_minus_3.required' => 'Vui lòng nhập Z-score -3',
            'z_score_minus_3.numeric' => 'Z-score -3 phải là một số',
            'z_score_minus_2.required' => 'Vui lòng nhập Z-score -2',
            'z_score_minus_2.numeric' => 'Z-score -2 phải là một số',
            'z_score_minus_1.required' => 'Vui lòng nhập Z-score -1',
            'z_score_minus_1.numeric' => 'Z-score -1 phải là một số',
            'z_score_0.required' => 'Vui lòng nhập Z-score 0',
            'z_score_0.numeric' => 'Z-score 0 phải là một số',
            'z_score_plus_1.required' => 'Vui lòng nhập Z-score +1',
            'z_score_plus_1.numeric' => 'Z-score +1 phải là một số',
            'z_score_plus_2.required' => 'Vui lòng nhập Z-score +2',
            'z_score_plus_2.numeric' => 'Z-score +2 phải là một số',
            'z_score_plus_3.required' => 'Vui lòng nhập Z-score +3',
            'z_score_plus_3.numeric' => 'Z-score +3 phải là một số',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
