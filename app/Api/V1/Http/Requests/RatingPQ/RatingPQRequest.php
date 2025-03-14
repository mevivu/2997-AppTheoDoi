<?php

namespace App\Api\V1\Http\Requests\RatingPQ;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidChild;
use App\Api\V1\Rules\ValidChildAge;


class RatingPQRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'limit' => 'nullable|integer',
            'page' => 'nullable|integer',
            'child_id' => ['required', 'exists:children,id'],

        ];
    }

    protected function methodPost(): array
    {
        return [
            'assessment_date' => 'required|date_format:Y-m-d',
            'height' => 'required|integer|min:1',
            'weight' => 'required|integer|min:1',
            'strength' => 'required|integer|min:0',
            'endurance' => 'required|integer|min:0',
            'child_id' => ['required', 'integer', new ValidChildAge()],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => 'required|integer|exists:ratings_pqs,id',
            'assessment_date' => 'required|date_format:Y-m-d',
            'height' => 'required|integer|min:1',
            'weight' => 'required|integer|min:1',
            'strength' => 'required|integer|min:0',
            'endurance' => 'required|integer|min:0',
            'child_id' => ['required', 'integer', new ValidChildAge()],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID đánh giá là bắt buộc.',
            'id.integer' => 'ID đánh giá phải là một số nguyên.',
            'id.exists' => 'ID đánh giá không tồn tại trong hệ thống.',
            'limit.required' => 'Số lượng là bắt buộc.',
            'limit.integer' => 'Số lượng phải là một số nguyên.',
            'limit.min' => 'Số lượng tối thiểu là 1.',
            'page.required' => 'Trang là bắt buộc.',
            'page.integer' => 'Trang phải là một số nguyên.',
            'page.min' => 'Trang tối thiểu là 1.',
            'child_id.required' => 'ID trẻ em là bắt buộc.',
            'child_id.numeric' => 'ID trẻ em phải là một số.',
            'child_id.exists' => 'ID trẻ em không tồn tại trong hệ thống.',
            'assessment_date.required' => 'Ngày đánh giá là bắt buộc.',
            'assessment_date.date_format' => 'Ngày đánh giá phải có định dạng YYYY-MM-DD.',
            'height.required' => 'Chiều cao là bắt buộc.',
            'height.integer' => 'Chiều cao phải là một số nguyên.',
            'height.min' => 'Chiều cao tối thiểu là 1 cm.',
            'weight.required' => 'Cân nặng là bắt buộc.',
            'weight.integer' => 'Cân nặng phải là một số nguyên.',
            'weight.min' => 'Cân nặng tối thiểu là 1 kg.',
            'strength.required' => 'Điểm sức mạnh là bắt buộc.',
            'strength.integer' => 'Điểm sức mạnh phải là một số nguyên.',
            'strength.min' => 'Điểm sức mạnh tối thiểu là 0.',
            'endurance.required' => 'Điểm sức bền là bắt buộc.',
            'endurance.integer' => 'Điểm sức bền phải là một số nguyên.',
            'endurance.min' => 'Điểm sức bền tối thiểu là 0.',

        ];
    }


}
