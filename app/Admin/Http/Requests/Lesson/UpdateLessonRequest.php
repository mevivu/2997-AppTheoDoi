<?php

namespace App\Admin\Http\Requests\Lesson;

class UpdateLessonRequest extends LessonRequest
{
    public function rules(): array
    {
        return $this->methodPut();
    }
}
