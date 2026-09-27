<?php

namespace App\Admin\Http\Requests\Lesson;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Lesson\LessonDifficulty;
use Illuminate\Validation\Rules\Enum;

class LessonRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'lesson_category_id' => ['required', 'exists:App\Models\LessonCategory,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'difficulty' => ['required', new Enum(LessonDifficulty::class)],
            'frequency' => ['nullable', 'string', 'max:100'],
            'benefit' => ['nullable', 'string'],
            'tools' => ['nullable', 'string'],
            'access_type' => ['required', new Enum(LessonAccessType::class)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'videos' => ['nullable', 'array'],
            'videos.*.id' => ['nullable', 'integer'],
            'videos.*.title' => ['nullable', 'string', 'max:255'],
            'videos.*.video_type' => ['nullable', 'string', 'in:youtube,r2'],
            'videos.*.video_url' => ['nullable', 'string', 'max:500'],
            'videos.*.video_path' => ['nullable', 'string', 'max:500'],
            'videos.*.thumbnail' => ['nullable', 'string', 'max:500'],
            'videos.*.duration_seconds' => ['nullable', 'integer', 'min:0'],
            'videos.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function methodPut(): array
    {
        return array_merge($this->methodPost(), [
            'id' => ['required', 'exists:App\Models\Lesson,id'],
        ]);
    }

    public function messages(): array
    {
        return [
            'lesson_category_id.required' => 'Vui lòng chọn danh mục bài học',
            'lesson_category_id.exists' => 'Danh mục bài học được chọn không hợp lệ',
            'name.required' => 'Vui lòng nhập tên bài học',
            'name.max' => 'Tên bài học không được vượt quá 255 ký tự',
            'difficulty.required' => 'Vui lòng chọn độ khó bài học',
            'access_type.required' => 'Vui lòng chọn loại phân quyền truy cập (Miễn phí hoặc Gói VIP)',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
