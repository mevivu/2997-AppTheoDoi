<?php

namespace App\Api\V1\Http\Requests\Journal;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ChildBelongsToUser;
use App\Enums\Journal\MilestoneStage;
use Illuminate\Validation\Rules\Enum;

class JournalMilestoneStoreRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'integer', new ChildBelongsToUser()],
            'stage' => ['required', new Enum(MilestoneStage::class)],
            'name' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:50'],
            'age_from_month' => ['nullable', 'integer', 'min:0', 'max:120'],
            'age_to_month' => ['nullable', 'integer', 'min:0', 'max:120'],
            'position' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $from = $this->input('age_from_month');
            $to = $this->input('age_to_month');
            if ($from !== null && $to !== null && (int)$to < (int)$from) {
                $validator->errors()->add('age_to_month', 'Tháng tuổi kết thúc phải lớn hơn hoặc bằng tháng bắt đầu.');
            }
        });
    }
}
