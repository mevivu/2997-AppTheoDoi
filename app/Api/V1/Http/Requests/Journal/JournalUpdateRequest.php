<?php

namespace App\Api\V1\Http\Requests\Journal;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Journal\JournalType;
use App\Models\Journal;
use App\Models\JournalMilestone;

class JournalUpdateRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'id' => 'required|integer|exists:journals,id',
            'milestone_id' => 'nullable|integer|exists:journal_milestones,id',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|array|min:1',
            'image.*' => 'file|image|max:5000',
            'created_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $journalId = $this->input('id');
            $milestoneId = $this->input('milestone_id');

            if ($milestoneId && $journalId) {
                $journal = Journal::find($journalId);
                if ($journal) {
                    if ($journal->type !== JournalType::Moment && $journal->type !== JournalType::Moment->value && $journal->type !== 'moment') {
                        $validator->errors()->add('milestone_id', 'Cột mốc chỉ áp dụng cho nhật ký khoảnh khắc.');
                        return;
                    }

                    $milestone = JournalMilestone::find($milestoneId);
                    if (!$milestone || !$milestone->is_active) {
                        $validator->errors()->add('milestone_id', 'Cột mốc này hiện không khả dụng.');
                        return;
                    }

                    $existing = Journal::where('child_id', $journal->child_id)
                        ->where('milestone_id', $milestoneId)
                        ->where('id', '!=', $journalId)
                        ->exists();

                    if ($existing) {
                        $validator->errors()->add('milestone_id', 'Bé đã có nhật ký khác cho cột mốc này.');
                    }
                }
            }
        });
    }
}
