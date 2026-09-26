<?php

namespace App\Admin\Http\Requests\ExerciseCategory;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseTopic;
use Illuminate\Validation\Rules\Enum;

class ExerciseCategoryRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'age_group_id' => ['required', 'exists:App\Models\AgeGroup,id'],
            'topic' => ['required', new Enum(ExerciseTopic::class)],
            'name' => ['required', 'string', 'max:200'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\ExerciseCategory,id'],
            'age_group_id' => ['required', 'exists:App\Models\AgeGroup,id'],
            'topic' => ['required', new Enum(ExerciseTopic::class)],
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
            'topic.required' => 'Vui lòng chọn chủ đề lớn (PQ, IQ, EQ, AQ, Thai giáo)',
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.max' => 'Tên danh mục không được vượt quá 200 ký tự',
            'icon.image' => 'File icon phải là hình ảnh',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
