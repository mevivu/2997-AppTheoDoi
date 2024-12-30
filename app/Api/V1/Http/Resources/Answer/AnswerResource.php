<?php

namespace App\Api\V1\Http\Resources\Answer;

use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'answer_id' => $this->id,
            'answer' => $this->answer,
        ];
    }
}
