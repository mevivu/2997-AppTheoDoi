<?php

namespace App\Api\V1\Http\Requests\HeightPrediction;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidUnBornChild;


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
            'child_id' => ['required', 'numeric', new ValidUnBornChild()],
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
