<?php

namespace App\Admin\Http\Requests\Guide;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Guide\GuideType;
use Illuminate\Validation\Rules\Enum;


class GuideRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class, false)],
            'type' => ['required', new Enum(GuideType::class, false), 'unique:guides,type'],
            'steps' => ['nullable', 'array'],
            'steps.*.title' => ['required', 'string'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.order' => ['required', 'integer'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:guides,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'type' => [
                'required',
                new Enum(GuideType::class),
                'unique:guides,type,' . request()->id
            ],
            'steps' => ['nullable', 'array'],
            'steps.*.title' => ['required', 'string'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.order' => ['required', 'integer'],
        ];
    }
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi ký tự.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.Enum' => 'Trạng thái không hợp lệ.',
            'type.required' => 'Loại hướng dẫn là bắt buộc.',
            'type.Enum' => 'Loại hướng dẫn không hợp lệ.',
            'type.unique' => 'Loại hướng dẫn này đã tồn tại.',
            'steps.array' => 'Các bước phải là một mảng.',
            'steps.*.title.required' => 'Tiêu đề của mỗi bước là bắt buộc.',
            'steps.*.title.string' => 'Tiêu đề của mỗi bước phải là một chuỗi ký tự.',
            'steps.*.description.string' => 'Mô tả của mỗi bước phải là một chuỗi ký tự.',
            'steps.*.order.required' => 'Thứ tự của mỗi bước là bắt buộc.',
            'steps.*.order.integer' => 'Thứ tự của mỗi bước phải là một số nguyên.',
            'id.required' => 'ID là bắt buộc khi cập nhật.',
            'id.exists' => 'ID không tồn tại trong hệ thống.',
        ];
    }
}
