<?php

namespace App\Api\V1\Http\Resources\Quiz;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Http\Resources\Question\QuestionResource;

class QuizEQAndAQResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($quiz) {
            return [
                'id' => $quiz->id,
                'type' => $quiz->type,
                'questions' => QuestionResource::collection($quiz->questions),
            ];
        });
    }
}
