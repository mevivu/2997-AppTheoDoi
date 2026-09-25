<?php

namespace App\Api\V1\Http\Requests\Memo;

use App\Api\V1\Http\Requests\BaseRequest;

class MemoCompetitionStartRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'integer', 'exists:children,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'Vui lòng chọn hồ sơ bé tham gia thi.',
            'child_id.exists' => 'Hồ sơ bé không tồn tại trong hệ thống.',
        ];
    }
}
