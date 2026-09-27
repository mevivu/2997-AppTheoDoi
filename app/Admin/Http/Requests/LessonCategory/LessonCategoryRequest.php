<?php

namespace App\Admin\Http\Requests\LessonCategory;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonCategoryKey;
use Illuminate\Validation\Rules\Enum;

class LessonCategoryRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'age_group_id' => ['required', 'exists:App\Models\AgeGroup,id'],
            'pillar' => ['required', new Enum(EducationPillar::class)],
            'key' => ['required', new Enum(LessonCategoryKey::class)],
            'name' => ['required', 'string', 'max:191'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\LessonCategory,id'],
            'age_group_id' => ['required', 'exists:App\Models\AgeGroup,id'],
            'pillar' => ['required', new Enum(EducationPillar::class)],
            'key' => ['required', new Enum(LessonCategoryKey::class)],
            'name' => ['required', 'string', 'max:191'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'age_group_id.required' => 'Vui lòng chọn nhóm tuổi',
            'pillar.required' => 'Vui lòng chọn lĩnh vực giáo dục (PQ, IQ, EQ, AQ, Thai giáo)',
            'key.required' => 'Vui lòng chọn định danh chuyên môn',
            'name.required' => 'Vui lòng nhập tên danh mục bài học',
            'name.max' => 'Tên danh mục không được vượt quá 191 ký tự',
            'icon.image' => 'File icon phải là hình ảnh',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
