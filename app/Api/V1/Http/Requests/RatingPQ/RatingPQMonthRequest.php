<?php

namespace App\Api\V1\Http\Requests\RatingPQ;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidChild;


class RatingPQMonthRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
//            'limit' => 'required|integer|min:1',
//            'page' => 'required|integer|min:1',
            'child_id' => ['required', 'numeric', new ValidChild()],
//            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:1900|max:' . date('Y'),

        ];
    }


    public function messages(): array
    {
        return [
            'limit.required' => 'Số lượng là bắt buộc.',
            'limit.integer' => 'Số lượng phải là một số nguyên.',
            'limit.min' => 'Số lượng tối thiểu là 1.',
            'page.required' => 'Trang là bắt buộc.',
            'page.integer' => 'Trang phải là một số nguyên.',
            'page.min' => 'Trang tối thiểu là 1.',
            'month.required' => 'Tháng là bắt buộc.',
            'month.integer' => 'Tháng phải là một số nguyên.',
            'month.min' => 'Tháng tối thiểu là 1.',
            'month.max' => 'Tháng tối đa là 12.',
            'year.required' => 'Năm là bắt buộc.',
            'year.integer' => 'Năm phải là một số nguyên.',
            'year.min' => 'Năm tối thiểu là 1900.',
            'year.max' => 'Năm không thể lớn hơn năm hiện tại.'
        ];
    }


}
