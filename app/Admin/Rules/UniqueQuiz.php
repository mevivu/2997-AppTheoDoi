<?php

namespace App\Admin\Rules;

use App\Models\Quiz;
use Illuminate\Contracts\Validation\Rule;

class UniqueQuiz implements Rule
{
    private  $type;
    private  $quizId;

    public function __construct($type, $quizId = null)
    {
        $this->type = $type;
        $this->quizId = $quizId;
    }

    public function passes($attribute, $value)
    {
        $query = Quiz::where('type', $this->type)->where('age', $value);

        if ($this->quizId) {
            $query->where('id', '!=', $this->quizId);
        }

        return $query->doesntExist();
    }

    public function message(): string
    {
        return 'Kết hợp giữa loại và độ tuổi đã tồn tại cho một bài kiểm tra khác.';
    }
}
