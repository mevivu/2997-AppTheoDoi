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
            $memoGames = MemoGameBuilderService::buildRounds((int) ($quiz->age ?? 1));
            $hasGame = !empty($memoGames);
            $gamePlays = count($memoGames);

            return [
                'id' => $quiz->id,
                'age' => $quiz->age,
                'type' => $quiz->type,
                'has_game' => $hasGame,
                'game_plays' => $gamePlays,
                'memo_games' => $memoGames,
                'questions' => QuestionResource::collection($quiz->questions),
            ];
        });
    }
}
