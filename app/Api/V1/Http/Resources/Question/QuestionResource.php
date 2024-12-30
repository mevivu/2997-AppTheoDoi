<?php

namespace App\Api\V1\Http\Resources\Question;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V1\Http\Resources\Answer\AnswerResource;

class QuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'question_id' => $this->id,
            'question' => $this->question,
            'question_type' => $this->question_type,
            'answers' => AnswerResource::collection($this->whenLoaded('answers')), // Eager load answers
        ];
    }
}
