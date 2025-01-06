<?php

namespace App\Api\V1\Http\Resources\Question;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V1\Http\Resources\Answer\AnswerResource;

class QuestionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'answers' => AnswerResource::collection($this->answers),
        ];
    }
}
