<?php

namespace App\Api\V1\Http\Requests\Memo;

use App\Api\V1\Http\Requests\BaseRequest;

class MemoCompetitionSubmitRoundRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('round_order') && !$this->has('game_number')) {
            $merge['game_number'] = $this->input('round_order');
        } elseif ($this->has('game_number') && !$this->has('round_order')) {
            $merge['round_order'] = $this->input('game_number');
        }

        if ($this->has('time_spent_seconds') && !$this->has('duration_spent')) {
            $merge['duration_spent'] = $this->input('time_spent_seconds');
        } elseif ($this->has('duration_spent') && !$this->has('time_spent_seconds')) {
            $merge['time_spent_seconds'] = $this->input('duration_spent');
        }

        if ($this->has('moves_count') && !$this->has('total_moves')) {
            $merge['total_moves'] = $this->input('moves_count');
        } elseif ($this->has('total_moves') && !$this->has('moves_count')) {
            $merge['moves_count'] = $this->input('total_moves');
        }

        if ($this->has('mistakes_count') && !$this->has('mistakes')) {
            $merge['mistakes'] = $this->input('mistakes_count');
        } elseif ($this->has('mistakes') && !$this->has('mistakes_count')) {
            $merge['mistakes_count'] = $this->input('mistakes');
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'integer', 'exists:children,id'],
            'entry_id' => ['required', 'integer', 'exists:memo_competition_entries,id'],
            'game_number' => ['required_without:round_order', 'nullable', 'integer', 'min:1', 'max:4'],
            'round_order' => ['required_without:game_number', 'nullable', 'integer', 'min:1', 'max:4'],
            'theme_id' => ['required', 'integer', 'exists:memo_themes,id'],
            'duration_spent' => ['required_without:time_spent_seconds', 'nullable', 'integer', 'min:0'],
            'time_spent_seconds' => ['required_without:duration_spent', 'nullable', 'integer', 'min:0'],
            'total_moves' => ['required_without:moves_count', 'nullable', 'integer', 'min:0'],
            'moves_count' => ['required_without:total_moves', 'nullable', 'integer', 'min:0'],
            'mistakes' => ['nullable', 'integer', 'min:0'],
            'mistakes_count' => ['nullable', 'integer', 'min:0'],
            'pairs_matched' => ['nullable', 'integer', 'min:0'],
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
            'game_number.required_without' => 'Thiếu thứ tự ván thi.',
            'game_number.min' => 'Thứ tự ván thi tối thiểu là 1.',
            'game_number.max' => 'Thứ tự ván thi tối đa là 4.',
            'round_order.required_without' => 'Thiếu thứ tự ván thi.',
            'round_order.min' => 'Thứ tự ván thi tối thiểu là 1.',
            'round_order.max' => 'Thứ tự ván thi tối đa là 4.',
            'theme_id.required' => 'Thiếu chủ đề của ván thi.',
            'theme_id.exists' => 'Chủ đề ván thi không tồn tại.',
            'duration_spent.required_without' => 'Thời gian chơi ván thi không được để trống.',
            'time_spent_seconds.required_without' => 'Thời gian chơi ván thi không được để trống.',
            'total_moves.required_without' => 'Số lần lật thẻ không được để trống.',
            'moves_count.required_without' => 'Số lần lật thẻ không được để trống.',
            'is_won.required' => 'Trạng thái thắng ván thi không được để trống.',
        ];
    }
}
