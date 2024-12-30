<?php

namespace App\Api\V1\Repositories\Quiz;

use App\Admin\Repositories\Quiz\QuizRepository as AdminRepository;

use App\Models\Quiz;

class QuizRepository extends AdminRepository implements QuizRepositoryInterface
{
    public function getAllQuizzesByTypeAndAge($age, $type)
    {
        return Quiz::with('questions.answers')
            ->where('age', $age)
            ->where('type', $type)
            ->get();
    }
}
