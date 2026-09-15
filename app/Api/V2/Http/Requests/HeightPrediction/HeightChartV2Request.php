<?php

namespace App\Api\V2\Http\Requests\HeightPrediction;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidChildPredictHeight;

class HeightChartV2Request extends BaseRequest
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
            'puberty_months' => ['required', 'numeric', 'min:0', 'max:96'],
            'target_height'  => ['nullable', 'numeric', 'min:50', 'max:250'],
        ];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'ID trẻ em là bắt buộc.',
            'child_id.numeric' => 'ID trẻ em phải là một số.',
            'puberty_months.required' => 'Số tháng dậy thì là bắt buộc.',
            'puberty_months.numeric' => 'Số tháng dậy thì phải là một số.',
            'puberty_months.min' => 'Số tháng dậy thì không được âm.',
            'puberty_months.max' => 'Số tháng dậy thì tối đa là 96 tháng.',
        ];
    }
}
