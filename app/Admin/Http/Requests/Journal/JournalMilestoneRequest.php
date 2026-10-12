<?php

namespace App\Admin\Http\Requests\Journal;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Journal\MilestoneStage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class JournalMilestoneRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'stage' => ['required', new Enum(MilestoneStage::class)],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:journal_milestones,slug'],
            'icon' => ['nullable', 'string'],
            'age_from_month' => ['required', 'integer', 'min:0', 'max:120'],
            'age_to_month' => ['required', 'integer', 'gte:age_from_month', 'max:120'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function methodPut(): array
    {
        $id = $this->input('id');
        return [
            'id' => ['required', 'exists:journal_milestones,id'],
            'stage' => ['required', new Enum(MilestoneStage::class)],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', Rule::unique('journal_milestones', 'slug')->ignore($id)],
            'icon' => ['nullable', 'string'],
            'age_from_month' => ['required', 'integer', 'min:0', 'max:120'],
            'age_to_month' => ['required', 'integer', 'gte:age_from_month', 'max:120'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'stage.required' => 'Giai đoạn phát triển là bắt buộc.',
            'name.required' => 'Tên cột mốc là bắt buộc.',
            'slug.unique' => 'Slug cột mốc đã tồn tại.',
            'age_from_month.required' => 'Độ tuổi bắt đầu (tháng) là bắt buộc.',
            'age_to_month.required' => 'Độ tuổi kết thúc (tháng) là bắt buộc.',
            'age_to_month.gte' => 'Độ tuổi kết thúc phải lớn hơn hoặc bằng độ tuổi bắt đầu.',
        ];
    }
}
