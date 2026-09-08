<?php

namespace App\Api\V2\Http\Requests\RatingPQ;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidChild;

class RatingPQGeneralInfoRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'child_id' => ['required', 'numeric', new ValidChild()],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'child_id là bắt buộc.',
            'child_id.numeric' => 'child_id phải là số.',
            'year.integer' => 'Năm phải là số nguyên.',
            'year.min' => 'Năm tối thiểu là 1900.',
            'year.max' => 'Năm không hợp lệ.',
        ];
    }
}
