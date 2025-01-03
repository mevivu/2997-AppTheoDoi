<?php

namespace App\Admin\Services\Question;

use App\Admin\Repositories\Answer\AnswerRepositoryInterface;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Enums\Answser\AnswerType;
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
        $question_id = $question->id;

        if ($data['answer']['answer_type'] == AnswerType::Image->value) {
            $images = $data['answer']['image-iq'];
            $answers = null;
        } else {
            $answers = $data['answer']['iq_answers'];
            $images = null;
        }
        $isCorrect = $data['answer']['is_correct'];

        if ($images) {
            foreach ($images as $index => $image) {
                $this->answerRepository->create([
                    'question_id' => $question_id,
                    'image' => $image,
                    'is_correct' => isset($isCorrect[$index][0]) && $isCorrect[$index][0] == '1' ? true : false,
                    'type' => AnswerType::Image->value,
                ]);
            }
        } else {
            foreach ($answers as $index => $answer) {
                $this->answerRepository->create([
                    'question_id' => $question_id,
                    'answer' => $answer,
                    'is_correct' => isset($isCorrect[$index][0]) && $isCorrect[$index][0] == '1' ? true : false,
                    'type' => AnswerType::Normal->value,
                ]);
            }
        }

        return $question;
    }

    protected function addEqAqQuestion($data, $question)
    {
        $question_id = $question->id;

        $scores = $data['answer']['scores'];

        if ($data['answer']['answer_type_aqeq'] == AnswerType::Image->value) {
            $answers = null;
            $images = $data['answer']['image-eqaq'];

            $images = array_values($images);
            $scores = array_values($scores);

            $result = [];
            foreach ($images as $key => $image) {
                $result[] = [
                    'image' => $image,
                    'score' => $scores[$key] ?? null,
                ];
            }

            foreach ($result as $item) {
                $this->answerRepository->create([
                    'question_id' => $question_id,
                    'image' => $item['image'],
                    'score' => $item['score'],
                    'type' => AnswerType::Image->value,
                ]);
            }
        } else {
            $answers = $data['answer']['answers'];
            $images = null;

            $answers = array_values($answers);
            $scores = array_values($scores);

            $result = [];
            foreach ($answers as $key => $answer) {
                $result[] = [
                    'answer' => $answer,
                    'score' => $scores[$key] ?? null, // Đảm bảo không bị lỗi nếu key không tồn tại
                ];
            }

            foreach ($result as $item) {
                $this->answerRepository->create([
                    'question_id' => $question_id,
                    'answer' => $item['answer'],
                    'score' => $item['score'],
                    'type' => AnswerType::Normal->value,
                ]);
            }
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
        $correctAnswerId = $data['answer']['is_correct'][$question->id] ?? null;
        $answerType = $data['answer']['answer_type'];

        // Lấy thông tin câu trả lời hiện có
        $existingAnswers = $this->answerRepository->getByQueryBuilder([
            'question_id' => $data['answer']['question_id'],
        ])->get();

        // Phân loại dữ liệu
        $answers = $answerType == AnswerType::Image->value
            ? $data['answer']['image-iq']
            : $data['answer']['iq_answers'];
        $answerKey = $answerType == AnswerType::Image->value ? 'image' : 'answer';

        // Lưu danh sách ID câu trả lời mới
        $newAnswerIds = [];

        foreach ($answers as $key => $content) {
            // Tìm câu trả lời cũ dựa trên nội dung
            $existingAnswer = $existingAnswers->where($answerKey, $content)->first();

            $isCorrectAnswer = false;
            if ($existingAnswer) {
                // Nếu câu trả lời cũ tồn tại
                $isCorrectAnswer = $correctAnswerId == $existingAnswer->id;
                $newAnswerIds[] = $existingAnswer->id;

                // Cập nhật dữ liệu câu trả lời
                $this->answerRepository->update($existingAnswer->id, [
                    $answerKey => $content,
                    'is_correct' => $isCorrectAnswer,
                    'question_id' => $data['answer']['question_id'],
                    'type' => $answerType,
                ]);
            } else {
                // Tạo mới câu trả lời
                $createdAnswer = $this->answerRepository->create([
                    $answerKey => $content,
                    'is_correct' => $correctAnswerId == $key, // So khớp với key từ frontend
                    'question_id' => $data['answer']['question_id'],
                    'type' => $answerType,
                ]);
                $newAnswerIds[] = $createdAnswer->id;
            }
        }

        // Xóa câu trả lời không còn sử dụng
        foreach ($existingAnswers as $existingAnswer) {
            if (!in_array($existingAnswer->id, $newAnswerIds)) {
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