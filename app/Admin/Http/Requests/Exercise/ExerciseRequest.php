<?php

namespace App\Admin\Http\Requests\Exercise;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseDifficulty;
use App\Enums\Exercise\ExerciseType;
use App\Enums\Video\VideoAccessType;
use Illuminate\Validation\Rules\Enum;

class ExerciseRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'exercise_type' => ['required', new Enum(ExerciseType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'exercise_category_id' => ['nullable', 'exists:App\Models\ExerciseCategory,id'],
            'content' => ['nullable', 'string'],
            'difficulty' => ['nullable', new Enum(ExerciseDifficulty::class)],
            'frequency' => ['nullable', 'string', 'max:100'],
            'benefit' => ['nullable', 'string'],
            'tools' => ['nullable', 'string'],
            'access_type' => ['nullable', new Enum(VideoAccessType::class)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'media_files' => ['nullable', 'array'],
            'media_files.*' => ['file', 'max:20480'], // max 20MB per media
            'media_types' => ['nullable', 'array'],
            'delete_media_ids' => ['nullable', 'array'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Exercise,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'exercise_type' => ['required', new Enum(ExerciseType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'exercise_category_id' => ['nullable', 'exists:App\Models\ExerciseCategory,id'],
            'content' => ['nullable', 'string'],
            'difficulty' => ['nullable', new Enum(ExerciseDifficulty::class)],
            'frequency' => ['nullable', 'string', 'max:100'],
            'benefit' => ['nullable', 'string'],
            'tools' => ['nullable', 'string'],
            'access_type' => ['nullable', new Enum(VideoAccessType::class)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'media_files' => ['nullable', 'array'],
            'media_files.*' => ['file', 'max:20480'],
            'media_types' => ['nullable', 'array'],
            'delete_media_ids' => ['nullable', 'array'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên bài tập',
            'name.string' => 'Tên bài tập phải là chuỗi',
            'description.string' => 'Mô tả phải là chuỗi',
            'exercise_type.required' => 'Vui lòng chọn loại bài tập',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}