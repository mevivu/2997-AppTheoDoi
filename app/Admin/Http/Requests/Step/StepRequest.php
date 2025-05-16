<?php

namespace App\Admin\Http\Requests\Step;

use App\Admin\Http\Requests\BaseRequest;


class StepRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'guide_id' => ['required', 'exists:guides,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'order' => ['required', 'integer'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:steps,id'],
            'guide_id' => ['nullable', 'exists:guides,id'],
            'title' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi ký tự.',
            'id.required' => 'ID là bắt buộc khi cập nhật.',
            'id.exists' => 'ID không tồn tại trong hệ thống.',
        ];
    }
}
