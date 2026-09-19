<?php

namespace App\Api\V1\Http\Resources\Quiz;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Http\Resources\Question\QuestionResource;

class QuizIQResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($quiz) {
            return [
                'id' => $quiz->id,
                'age' => $quiz->age,
                'type' => $quiz->type,
                'game_plays' => (int) ($quiz->game_plays ?? 3),
                'questions' => QuestionResource::collection($quiz->questions),
            ];
        });
    }
}
