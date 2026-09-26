<?php

namespace App\Admin\Http\Requests\VideoCategory;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;

class VideoCategoryRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'age_group_id' => ['required', 'exists:App\Models\AgeGroup,id'],
            'name' => ['required', 'string', 'max:200'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\VideoCategory,id'],
            'age_group_id' => ['required', 'exists:App\Models\AgeGroup,id'],
            'name' => ['required', 'string', 'max:200'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'age_group_id.required' => 'Vui lòng chọn nhóm tuổi',
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.max' => 'Tên danh mục không được vượt quá 200 ký tự',
            'icon.image' => 'File icon phải là hình ảnh',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
