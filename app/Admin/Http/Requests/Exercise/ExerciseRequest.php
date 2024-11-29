<?php

namespace App\Admin\Http\Requests\Exercise;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseType;
use Illuminate\Validation\Rules\Enum;

class ExerciseRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'exercise_type' => ['required', new Enum(ExerciseType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\Exercise,id'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'exercise_type' => ['required', new Enum(ExerciseType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
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