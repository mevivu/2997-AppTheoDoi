<?php

namespace App\Api\V1\Http\Requests\HeightPrediction;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidChildPredictHeight;


class HeightPredictionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'child_id' => ['required', 'numeric', new ValidChildPredictHeight()],
        ];
    }



    public function messages(): array
    {
        return [
            'child_id.required' => 'ID trẻ em là bắt buộc.',
            'child_id.numeric' => 'ID trẻ em phải là một số.',
            'child_id.exists' => 'ID trẻ em không tồn tại trong hệ thống.',

        ];
    }


}
