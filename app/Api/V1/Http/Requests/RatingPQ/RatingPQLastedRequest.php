<?php

namespace App\Api\V1\Http\Requests\RatingPQ;

use App\Api\V1\Http\Requests\BaseRequest;



class RatingPQLastedRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [

            'child_id' => ['required', 'exists:children,id'],

        ];
    }



    public function messages(): array
    {
        return [
            'child_id.required' => 'child_id là bắt buộc.',
            'child_id.exists' => 'child_id tồn tại trong hệ thống.',


        ];
    }


}
