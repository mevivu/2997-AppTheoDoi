<?php

namespace App\Api\V1\Http\Requests\AppFeedback;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Feedback\AppFeedbackFeature;
use Illuminate\Validation\Rule;

class StoreAppFeedbackRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'favorite_feature' => ['required', 'string', Rule::in(AppFeedbackFeature::options())],
            'improvement_feedback' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Vui lòng chọn mức độ hài lòng từ 1 đến 5 sao.',
            'rating.integer' => 'Mức độ hài lòng không hợp lệ.',
            'rating.min' => 'Mức độ hài lòng tối thiểu là 1 sao.',
            'rating.max' => 'Mức độ hài lòng tối đa là 5 sao.',
            'favorite_feature.required' => 'Vui lòng chọn tính năng bạn hài lòng nhất.',
            'favorite_feature.in' => 'Tính năng lựa chọn không hợp lệ.',
            'improvement_feedback.required' => 'Vui lòng nhập nội dung mong muốn Chăm Con 360 cải thiện.',
            'improvement_feedback.max' => 'Nội dung góp ý tối đa 500 ký tự.',
        ];
    }
}
