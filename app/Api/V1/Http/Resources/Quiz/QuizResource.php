<?php

namespace App\Api\V1\Http\Resources\Quiz;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Http\Resources\Question\QuestionResource;

class QuizResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($quiz) {
            // Kiểm tra xem các câu hỏi đã được tải hay chưa, nếu chưa thì tải chúng
            $quiz->load('questions.answers');

            return [
                'quiz_id' => $quiz->id,
                'age' => $quiz->age,
                'type' => $quiz->type,
                'questions' => QuestionResource::collection($quiz->questions),
            ];
        });
    }
}
