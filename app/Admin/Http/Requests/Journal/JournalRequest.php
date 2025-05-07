<?php

namespace App\Admin\Http\Requests\Journal;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Journal\JournalType;
use Illuminate\Validation\Rules\Enum;


class JournalRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'exists:App\Models\Child,id'],
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'type' => ['nullable', new Enum(JournalType::class)],
            'image' => ['required', 'array', 'min:1'],
            'image.*' => ['required', 'string', 'distinct', 'not_in:""'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Journal,id'],
            'child_id' => ['nullable', 'exists:App\Models\Child,id'],
            'title' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'type' => ['nullable', new Enum(JournalType::class)],
            'image' => ['nullable', 'array', 'min:1'],
            'image.*' => ['nullable', 'string', 'distinct', 'not_in:""'],
        ];
    }



    public function messages(): array
    {
        return [
            'child_id.required' => 'Trường child_id là bắt buộc.',
            'child_id.exists' => 'Không tìm thấy thông tin child_id tương ứng.',
            'title.required' => 'Trường tiêu đề là bắt buộc.',
            'content.required' => 'Trường nội dung là bắt buộc.',
            'type.nullable' => 'Loại nhật ký không hợp lệ.',
            'image.required' => 'Bạn cần cung cấp ít nhất một hình ảnh.',
            'image.array' => 'Dữ liệu ảnh cần được cung cấp dưới dạng mảng.',
            'image.min' => 'Bạn cần cung cấp ít nhất một hình ảnh.',
            'image.*.required' => 'Tất cả hình ảnh đều bắt buộc.',
            'image.*.string' => 'Dữ liệu hình ảnh phải là chuỗi.',
            'image.*.distinct' => 'Hình ảnh không được trùng lặp.',
            'image.*.not_in' => 'Dữ liệu hình ảnh không hợp lệ.'
        ];
    }
}
