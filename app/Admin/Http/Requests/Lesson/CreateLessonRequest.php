<?php

namespace App\Admin\Http\Requests\Lesson;

class CreateLessonRequest extends LessonRequest
{
    public function rules(): array
    {
        return $this->methodPost();
    }
}
