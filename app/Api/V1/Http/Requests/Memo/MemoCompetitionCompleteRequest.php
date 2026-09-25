<?php

namespace App\Api\V1\Http\Requests\Memo;

use App\Api\V1\Http\Requests\BaseRequest;

class MemoCompetitionCompleteRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'integer', 'exists:children,id'],
            'entry_id' => ['required', 'integer', 'exists:memo_competition_entries,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'Thiếu child_id của bé.',
            'child_id.exists' => 'Hồ sơ bé không tồn tại.',
            'entry_id.required' => 'Thiếu entry_id của lượt thi.',
            'entry_id.exists' => 'Lượt thi không tồn tại.',
        ];
    }
}
