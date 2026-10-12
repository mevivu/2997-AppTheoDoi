<?php

namespace App\Api\V1\Http\Requests\Journal;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ChildBelongsToUser;
use App\Enums\Journal\JournalType;
use App\Models\Journal;
use App\Models\JournalMilestone;
use Illuminate\Validation\Rules\Enum;

class JournalRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'limit' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'type' => ['required', new Enum(JournalType::class)],
            'child_id' => ['required', new ChildBelongsToUser()],
            'date' => 'nullable|date_format:d-m-Y',
        ];
    }

    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', new ChildBelongsToUser()],
            'milestone_id' => ['nullable', 'integer', 'exists:journal_milestones,id'],
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'type' => ['required', new Enum(JournalType::class)],
            'image' => 'nullable|array|min:1',
            'image.*' => 'file|image|max:5000',
            'created_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $milestoneId = $this->input('milestone_id');
            $childId = $this->input('child_id');
            $type = $this->input('type');

            if ($milestoneId) {
                // Milestone chỉ áp dụng cho moment
                if ($type !== JournalType::Moment->value && $type !== 'moment') {
                    $validator->errors()->add('milestone_id', 'Cột mốc chỉ áp dụng cho nhật ký khoảnh khắc.');
                    return;
                }

                $milestone = JournalMilestone::find($milestoneId);
                if (!$milestone || !$milestone->is_active) {
                    $validator->errors()->add('milestone_id', 'Cột mốc này hiện không khả dụng.');
                    return;
                }

                $existing = Journal::where('child_id', $childId)
                    ->where('milestone_id', $milestoneId)
                    ->first();

                if ($existing) {
                    $validator->errors()->add('milestone_id', "Bé đã có nhật ký cho cột mốc này (ID: {$existing->id}).");
                }
            }
        });
    }
}
