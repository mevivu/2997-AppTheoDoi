<?php

namespace App\Api\V1\Http\Requests\HeightPrediction;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidChildPredictHeight;

class HeightChartRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'child_id'       => ['required', 'numeric', new ValidChildPredictHeight()],
            'target_height'  => ['required', 'numeric', 'min:50', 'max:250'],
            'puberty_months' => ['nullable', 'numeric', 'min:0', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'ID trẻ em là bắt buộc.',
            'child_id.numeric' => 'ID trẻ em phải là một số.',
            'child_id.exists' => 'ID trẻ em không tồn tại trong hệ thống.',
            'target_height.required' => 'Mục tiêu chiều cao là bắt buộc.',
            'target_height.numeric' => 'Mục tiêu chiều cao phải là một số.',
            'target_height.min' => 'Mục tiêu chiều cao tối thiểu là 50cm.',
            'target_height.max' => 'Mục tiêu chiều cao tối đa là 250cm.',
            'puberty_months.numeric' => 'Số tháng dậy thì phải là một số.',
            'puberty_months.min' => 'Số tháng dậy thì không được âm.',
            'puberty_months.max' => 'Số tháng dậy thì không hợp lệ.',
        ];
    }
}
