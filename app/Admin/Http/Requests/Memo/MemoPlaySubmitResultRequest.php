<?php

namespace App\Admin\Http\Requests\Memo;

use App\Admin\Http\Requests\BaseRequest;

class MemoPlaySubmitResultRequest extends BaseRequest
{
    /**
     * Quy tắc xác thực khi lưu kết quả bài test (POST)
     */
    protected function methodPost(): array
    {
        return [
            'theme_id' => ['required', 'integer', 'exists:memo_themes,id'],
            'age_config_id' => ['required', 'integer', 'exists:memo_age_configs,id'],
            'total_duration_spent' => ['required', 'integer', 'min:0'],
            'total_pairs_matched' => ['required', 'integer', 'min:0'],
            'total_mistakes' => ['required', 'integer', 'min:0'],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'evaluation_label' => ['required', 'string', 'max:191'],
            'rounds' => ['nullable', 'array'],
            'rounds.*.round_number' => ['nullable', 'integer', 'min:1'],
            'rounds.*.duration_spent' => ['nullable', 'integer', 'min:0'],
            'rounds.*.pairs_matched' => ['nullable', 'integer', 'min:0'],
            'rounds.*.mistakes' => ['nullable', 'integer', 'min:0'],
            'rounds.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * Thông báo lỗi xác thực
     */
    public function messages(): array
    {
        return [
            'theme_id.required' => 'Vui lòng chọn chủ đề bài test.',
            'theme_id.exists' => 'Chủ đề đã chọn không tồn tại trong hệ thống.',
            'age_config_id.required' => 'Vui lòng chọn cấu hình độ tuổi.',
            'age_config_id.exists' => 'Cấu hình độ tuổi đã chọn không tồn tại.',
            'total_duration_spent.required' => 'Thời gian hoàn thành không được để trống.',
            'total_pairs_matched.required' => 'Số cặp ghép đúng không được để trống.',
            'total_mistakes.required' => 'Số lần lật sai không được để trống.',
            'score.required' => 'Điểm số bài test không được để trống.',
            'score.min' => 'Điểm số tối thiểu là 0.',
            'score.max' => 'Điểm số tối đa là 100.',
            'evaluation_label.required' => 'Nhãn xếp loại đánh giá không được để trống.',
        ];
    }
}
