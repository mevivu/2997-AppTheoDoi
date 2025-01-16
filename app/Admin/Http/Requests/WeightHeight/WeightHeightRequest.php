<?php

namespace App\Admin\Http\Requests\WeightHeight;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class WeightHeightRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'weight'=>['required','numeric'],
            'height'=>['required','numeric'],
            'age'=>['required','integer'],
            'month'=>['required','numeric','between:1,12'],
            'weight_change' => ['required', 'numeric'],
            'height_change' => ['required', 'numeric'],
            'gender'=>['required',new Enum(Gender::class)],
            'status'=>['required',new Enum(ActiveStatus::class)],
        ];
    }
    protected function methodPut(): array
    {
        return [
            'id'=>['required','exists:App\Models\WeightHeightWHO,id'],
            'weight'=>['required','numeric'],
            'height'=>['required','numeric'],
            'age'=>['required','integer'],
            'weight_change' => ['required', 'numeric'],
            'height_change' => ['required', 'numeric'],
            'month'=>['required','numeric','between:1,12'],
            'gender'=>['required',new Enum(Gender::class)],
            'status'=>['required',new Enum(ActiveStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'weight.required' => 'Vui lòng nhập cân nặng.',
            'weight.numeric' => 'Cân nặng phải là một số.',
            'height.required' => 'Vui lòng nhập chiều cao.',
            'height.numeric' => 'Chiều cao phải là một số.',
            'age.required' => 'Vui lòng nhập tuổi.',
            'age.integer' => 'Tuổi phải là số nguyên.',
            'month.required' => 'Vui lòng nhập tháng.',
            'month.numeric' => 'Tháng phải là một số.',
            'month.between' => 'Tháng phải nằm trong khoảng từ 1 đến 12.',
            'gender.required' => 'Vui lòng chọn giới tính.',
            'status.required' => 'Vui lòng chọn trạng thái hoạt động.',
            'weight_change.numeric' => 'Thay đổi cân nặng phải là một số.',
            'height_change.numeric' => 'Thay đổi chiều cao phải là một số.'
        ];
    }



}
