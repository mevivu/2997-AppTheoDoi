<?php

namespace App\Admin\Services\Question;

use App\Admin\Repositories\Answer\AnswerRepositoryInterface;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Enums\Answser\AnswerType;
use Exception;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class QuestionService implements QuestionServiceInterface
{

    protected QuestionRepositoryInterface $repository;
    protected AnswerRepositoryInterface $answerRepository;

    protected FileService $fileService;

    public function __construct(
        QuestionRepositoryInterface $repository,
        AnswerRepositoryInterface   $answerRepository,
        FileService                 $fileService
    )
    {
        $this->repository = $repository;
        $this->answerRepository = $answerRepository;
        $this->fileService = $fileService;
    }

    public function storeIq(Request $request): object|bool
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $questionData = $data['question'];
            $questionImage = $data['question']['question_image'] ?? null;
            if ($questionImage) {
                $questionData['question_image'] =
                    $this->fileService->uploadAvatar('images/questions', $questionImage);
            }
            $isCorrect = $data['answers']['is_correct'];

            if ($data['answers']['type'] == AnswerType::Normal->value) {
                $answerData = $data['answers']['answer'];
            } else {
                $answerData = $data['answers']['image'];
            }

            $question = $this->repository->create($questionData);

            foreach ($answerData as $key => $value) {
                $answer = [
                    'question_id' => $question->id,
                    'answer' => $value,
                    'is_correct' => $key == $isCorrect ? true : false,
                    'type' => $data['answers']['type']
                ];
                $this->answerRepository->create($answer);
            }

            DB::commit();
            return $question;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }

    public function updateIq(Request $request): object|bool
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $questionData = $data['question'];
            $questionImage = $data['question']['question_image'] ?? null;
            $isCorrect = $data['answers']['is_correct'];
            $question = $this->repository->findOrFail($questionData['id']);
            if ($questionImage) {
                $questionData['question_image'] =
                    $this->fileService->uploadAvatar('images/questions', $questionImage, $question->question_image);
            }
            else{
                $questionData['question_image'] = null;
            }
            if ($data['answers']['type'] == AnswerType::Normal->value) {
                $answerData = $data['answers']['answer'];
            } else {
                $answerData = $data['answers']['image'];
            }

            $question->update($questionData);
            $this->answerRepository->deleteWhere(['question_id' => $question->id]);
            $index = 0;
            foreach ($answerData as $key => $value) {
                $correct = $index == $isCorrect;
                $answer = [
                    'question_id' => $question->id,
                    'answer' => $value,
                    'is_correct' => $correct,
                    'type' => $data['answers']['type']
                ];
                $this->answerRepository->create($answer);
                $index++;
            }

            DB::commit();
            return $question;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }

    public function storeEqAq(Request $request): object|bool
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $questionData = $data['question'];
            $question = $this->repository->create($questionData);
            if ($data['answers']['type'] == AnswerType::Normal->value) {
                $answerData = $data['answers']['answer'];
            } else {
                $answerData = $data['answers']['image'];
            }

            foreach ($answerData as $key => $value) {
                $answer = [
                    'question_id' => $question->id,
                    'answer' => $value,
                    'score' => $data['answers']['score'][$key],
                    'type' => $data['answers']['type']
                ];
                $this->answerRepository->create($answer);
            }

            DB::commit();
            return $question;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }

    public function updateEqAq(Request $request): object|bool
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $questionData = $data['question'];
            $question = $this->repository->update($questionData['id'], $questionData);
            $existAnswers = $question->answers->pluck('answer', 'id')->toArray();

            if ($data['answers']['type'] == AnswerType::Normal->value) {
                $answerData = $data['answers']['answer'];
            } else {
                $answerData = $data['answers']['image'];
            }

            foreach ($answerData as $key => $value) {
                if (isset($existAnswers[$key])) {
                    $this->answerRepository->update($key,
                        [
                            'answer' => $value, 'score' => $data['answers']['score'][$key]
                        ]);
                    unset($existAnswers[$key]);
                } else {
                    $answer = [
                        'question_id' => $question->id,
                        'answer' => $value,
                        'score' => $data['answers']['score'][$key],
                        'type' => $data['answers']['type']
                    ];
                    $this->answerRepository->create($answer);
                }
            }

            if (count($existAnswers) > 0) {
                $this->answerRepository->deleteMany(array_keys($existAnswers));
            }

            DB::commit();
            return $question;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
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
