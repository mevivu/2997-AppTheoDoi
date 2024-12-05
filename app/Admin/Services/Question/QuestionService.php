<?php

namespace App\Admin\Services\Question;

use App\Admin\Repositories\Answer\AnswerRepositoryInterface;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Enums\Question\QuestionType;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;

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
        $question_id = $question->id;
        $correctAnswer = $data['answer']['correct_answer'];
        $wrongAnswers = $data['answer']['wrong_answers'];

        $this->answerRepository->create([
            'question_id' => $question_id,
            'answer' => $correctAnswer,
            'is_correct' => true,
        ]);

        foreach ($wrongAnswers as $answer) {
            $this->answerRepository->create([
                'question_id' => $question_id,
                'answer' => $answer,
                'is_correct' => false,
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

        $correctAnswer = $data['answer']['correct_answer'];
        $correctAnswerId = $data['answer']['correct_answer_id'];

        // Cập nhật câu trả lời đúng
        $this->answerRepository->update($correctAnswerId, [
            'answer' => $correctAnswer,
            'is_correct' => true,
            'question_id' => $data['answer']['question_id'],
        ]);

        $existingWrongAnswers = $this->answerRepository->getByQueryBuilder([
            'question_id' => $data['answer']['question_id'],
            'is_correct' => false,
        ])->get();

        // Xử lý các câu trả lời sai
        foreach ($data['answer']['wrong_answers'] as $wrongAnswerId => $wrongAnswer) {
            $existingAnswer = $this->answerRepository->find($wrongAnswerId);

            if ($existingAnswer) {
                $this->answerRepository->update($wrongAnswerId, [
                    'answer' => $wrongAnswer,
                    'is_correct' => false,
                    'question_id' => $data['answer']['question_id'],
                ]);
            } else {
                $this->answerRepository->create([
                    'answer' => $wrongAnswer,
                    'is_correct' => false,
                    'question_id' => $data['answer']['question_id'],
                ]);
            }
        }

        foreach ($existingWrongAnswers as $wrongAnswer) {
            if (!isset($data['answer']['wrong_answers'][$wrongAnswer->id])) {
                $this->answerRepository->delete($wrongAnswer->id);
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
