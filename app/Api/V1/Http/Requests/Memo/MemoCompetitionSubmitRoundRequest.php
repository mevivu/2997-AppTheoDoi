<?php

namespace App\Api\V1\Http\Requests\Memo;

use App\Api\V1\Http\Requests\BaseRequest;

class MemoCompetitionSubmitRoundRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'integer', 'exists:children,id'],
            'entry_id' => ['required', 'integer', 'exists:memo_competition_entries,id'],
            'round_order' => ['required', 'integer', 'min:1', 'max:4'],
            'theme_id' => ['required', 'integer', 'exists:memo_themes,id'],
            'time_spent_seconds' => ['required', 'integer', 'min:0'],
            'moves_count' => ['required', 'integer', 'min:0'],
            'mistakes_count' => ['nullable', 'integer', 'min:0'],
            'is_won' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'Thiếu child_id của bé tham gia thi.',
            'child_id.exists' => 'Hồ sơ bé không tồn tại.',
            'entry_id.required' => 'Thiếu entry_id của lượt thi.',
            'entry_id.exists' => 'Lượt thi không tồn tại.',
            'round_order.required' => 'Thiếu thứ tự ván thi.',
            'round_order.min' => 'Thứ tự ván thi tối thiểu là 1.',
            'round_order.max' => 'Thứ tự ván thi tối đa là 4.',
            'theme_id.required' => 'Thiếu chủ đề của ván thi.',
            'theme_id.exists' => 'Chủ đề ván thi không tồn tại.',
            'time_spent_seconds.required' => 'Thời gian chơi ván thi không được để trống.',
            'moves_count.required' => 'Số lần lật thẻ không được để trống.',
            'is_won.required' => 'Trạng thái thắng ván thi không được để trống.',
        ];
    }
}
