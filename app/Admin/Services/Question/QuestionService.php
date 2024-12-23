<?php

namespace App\Admin\Services\Question;

use App\Admin\Repositories\Answer\AnswerRepositoryInterface;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Enums\Question\QuestionType;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;
use Illuminate\Support\Facades\DB;

class QuestionService implements QuestionServiceInterface
{
    protected $repository;
    protected $answerRepository;

    public function __construct(
        QuestionRepositoryInterface $repository,
        AnswerRepositoryInterface $answerRepository
    ) {
        $this->repository = $repository;
        $this->answerRepository = $answerRepository;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        $question = $this->repository->create($data['question']);

        switch ($data['question']['question_type']) {
            case QuestionType::IQ->value:
                return $this->addIqQuestion($data, $question);
            case QuestionType::EQ->value:
                return $this->addEqAqQuestion($data, $question);
            case QuestionType::AQ->value:
                return $this->addEqAqQuestion($data, $question);
        }
    }

    protected function addIqQuestion($data, $question)
    {

        //     "answer" => array:2 [▼
        //     "is_correct" => array:3 [▼
        //       0 => array:1 [▼
        //         0 => "0"
        //       ]
        //       1 => array:1 [▼
        //         0 => "1"
        //       ]
        //       2 => array:1 [▼
        //         0 => "0"
        //       ]
        //     ]
        //     "iq_answers" => array:3 [▼
        //       0 => "213"
        //       1 => "cc"
        //       2 => "đ"
        //     ]
        //   ]
        // ]
        $question_id = $question->id;

        $answers = $data['answer']['iq_answers'];
        $isCorrect = $data['answer']['is_correct'];

        foreach ($answers as $index => $answer) {
            $this->answerRepository->create([
                'question_id' => $question_id,
                'answer' => $answer,
                'is_correct' => isset($isCorrect[$index][0]) && $isCorrect[$index][0] == '1' ? true : false,
            ]);
        }

        return $question;
    }

    protected function addEqAqQuestion($data, $question)
    {
        $question_id = $question->id;
        $answers = $data['answer']['answers'];
        $scores = $data['answer']['scores'];

        foreach ($answers as $index => $answer) {
            $this->answerRepository->create([
                'question_id' => $question_id,
                'answer' => $answer,
                'score' => $scores[$index],
            ]);
        }

        return $question;
    }

    public function update(Request $request)
    {
        $data = $request->validated();

        $question = $this->repository->update($data['question']['id'], $data['question']);

        switch ($data['question']['question_type']) {
            case QuestionType::IQ->value:
                return $this->updateIqQuestion($data, $question);
            case QuestionType::EQ->value:
                return $this->updateEqAqQuestion($data, $question);
            case QuestionType::AQ->value:
                return $this->updateEqAqQuestion($data, $question);
        }
    }



    protected function updateIqQuestion($data, $question)
    {
        $data['answer']['question_id'] = $question->id;
        $answers = $data['answer']['iq_answers'];
        $correctAnswerId = $data['answer']['is_correct'][$question->id];

        // dd($answers, $correctAnswerId);

        $existingAnswers = $this->answerRepository->getByQueryBuilder([
            'question_id' => $data['answer']['question_id'],
        ])->get();

        foreach ($answers as $answerId => $answer) {
            if ($answerId == $correctAnswerId) {
                $isCorrectAnswer = true;
            } else {
                $isCorrectAnswer = false;
            }

            // dd($answerId, $answer, $isCorrectAnswer);

            $existingAnswer = $existingAnswers->firstWhere('id', $answerId);
            // dd($existingAnswer);
            if ($existingAnswer) {
                $existingAnswer->update([
                    'answer' => $answer,
                    'is_correct' => $isCorrectAnswer,
                    'question_id' => $data['answer']['question_id'],
                ]);
            } else {
                $this->answerRepository->create([
                    'answer' => $answer,
                    'is_correct' => $isCorrectAnswer,
                    'question_id' => $data['answer']['question_id'],
                ]);
            }
        }

        foreach ($existingAnswers as $existingAnswer) {
            if (!isset($answers[$existingAnswer->id])) {
                $existingAnswer->delete();
            }
        }
    }


    protected function updateEqAqQuestion($data, $question)
    {
        $data['answer']['question_id'] = $question->id;

        $answers = $data['answer']['answers'];
        $scores = $data['answer']['scores'];

        $existingAnswers = $this->answerRepository->getByQueryBuilder([
            'question_id' => $data['answer']['question_id'],
        ])->get();

        foreach ($answers as $answerId => $answer) {
            $score = $scores[$answerId];
            $existingAnswer = $existingAnswers->where('answer', $answer)->first();

            if ($existingAnswer) {
                $this->answerRepository->update($existingAnswer->id, [
                    'answer' => $answer,
                    'score' => $score,
                    'question_id' => $data['answer']['question_id'],
                ]);
            } else {
                $this->answerRepository->create([
                    'answer' => $answer,
                    'score' => $score,
                    'question_id' => $data['answer']['question_id'],
                ]);
            }
        }

        foreach ($existingAnswers as $existingAnswer) {
            if (!isset($answers[$existingAnswer->id])) {
                $this->answerRepository->delete($existingAnswer->id);
            }
        }
    }



    public function actionMultipleRecords(Request $request): bool
    {
        $data = $request->all();

        switch ($data['action']) {
            case ActiveStatus::Active->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Active);
                }
                return true;
            case ActiveStatus::Draft->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Draft);
                }
                return true;
            case ActiveStatus::Deleted->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Deleted);
                }
                return true;

            default:
                return false;
        }
    }

}