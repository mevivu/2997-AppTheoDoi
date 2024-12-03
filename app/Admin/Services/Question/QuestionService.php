<?php

namespace App\Admin\Services\Question;

use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Enums\Question\QuestionType;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;

class QuestionService implements QuestionServiceInterface
{
    protected $repository;

    public function __construct(
        QuestionRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $data = $request->validated();

        switch ($data['question_type']) {
            case QuestionType::IQ->value:
                return $this->addIqQuestion($data);
            case QuestionType::EQ->value:
                $data['question_type'] = QuestionType::EQ;
                break;
            case QuestionType::AQ->value:
                $data['question_type'] = QuestionType::AQ;
                break;
        }
    }

    protected function addIqQuestion($data)
    {
        $data['wrong_answers'] = json_encode($data['wrong_answers']);
        $data['question_group_id'] = null;
        $data['answer'] = null;
        $data['score'] = null;
        return $this->repository->create($data);
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        return $this->repository->update($data['id'], $data);
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
