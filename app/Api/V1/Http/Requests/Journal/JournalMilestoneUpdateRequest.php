<?php

namespace App\Api\V1\Http\Requests\Journal;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Journal\MilestoneStage;
use Illuminate\Validation\Rules\Enum;

class JournalMilestoneUpdateRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'stage' => ['nullable', new Enum(MilestoneStage::class)],
            'name' => ['nullable', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:50'],
            'age_from_month' => ['nullable', 'integer', 'min:0', 'max:120'],
            'age_to_month' => ['nullable', 'integer', 'min:0', 'max:120'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
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
