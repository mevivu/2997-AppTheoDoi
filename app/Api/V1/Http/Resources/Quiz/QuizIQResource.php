<?php

namespace App\Api\V1\Http\Resources\Quiz;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Http\Resources\Question\QuestionResource;
use App\Api\V1\Services\Memo\MemoGameBuilderService;

class QuizIQResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($quiz) {
            $gamePlays = (int) ($quiz->game_plays ?? 3);
            $memoGames = MemoGameBuilderService::buildRounds((int) ($quiz->age ?? 1), $gamePlays);
            $hasGame = !empty($memoGames);

            return [
                'id' => $quiz->id,
                'age' => $quiz->age,
                'type' => $quiz->type,
                'has_game' => $hasGame,
                'game_plays' => $hasGame ? $gamePlays : 0,
                'memo_games' => $memoGames,
                'questions' => QuestionResource::collection($quiz->questions),
            ];
        });
    }
}
