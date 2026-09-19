<?php

namespace App\Admin\Services\Quiz;


use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Admin\Repositories\Quiz\QuizRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\ActiveStatus;
use App\Enums\Question\AgeGroup;
use App\Enums\Question\QuestionType;
use App\Enums\Random;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class QuizSizeService implements QuizServiceInterface
{
    use Setup, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected QuizRepositoryInterface $repository;

    protected QuestionRepositoryInterface $questionRepository;


    public function __construct(
        QuizRepositoryInterface     $repository,
        QuestionRepositoryInterface $questionRepository

    )
    {
        $this->repository = $repository;
        $this->questionRepository = $questionRepository;
    }

    public function checkTypeExists(array $types): bool
    {
        // Kiểm tra nếu có bất kỳ quiz nào với type EQ hoặc AQ
        return $this->repository->existsWithTypes($types);
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();
        $questionIds = json_decode($data['selected_questions'] ?? '[]', true);

        $quiz = $this->repository->create($data);
        if (!empty($questionIds)) {
            $quiz->questions()->attach($questionIds);
        }
        return $quiz;
    }

    /**
     * @throws Exception
     */
    public function storeIQ(Request $request): object|false
    {
        $data = $request->validated();
        $data['status'] = ActiveStatus::Active;
        $questionIds = json_decode($data['selected_questions'] ?? '[]', true);

        $quiz = $this->repository->create($data);
        if (!empty($questionIds)) {
            $quiz->questions()->attach($questionIds);
        }
        return $quiz;
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {

        $data = $request->validated();
        $random = $data['random'] ?? null;
        $ageGroup = $data['age_group'] ?? null;
        $type = $data['type'];
        if ($random === Random::YES->value && $type === QuestionType::EQ->value) {
            $questions = $this->questionRepository->getQueryBuilder()
                ->where('age_group', $ageGroup)
                ->where('question_type', QuestionType::EQ)
                ->where('status', ActiveStatus::Active)
                ->get();

            $groupedQuestions = $questions->groupBy('question_group_id');
            $randomQuestions = $groupedQuestions->map(function ($groupQuestions) {
                return $groupQuestions->random();
            });

            $questionIds = $randomQuestions->pluck('id')->toArray();
        } else {
            $questionIds = json_decode($data['selected_questions'] ?? '[]', true);
        }
        $quiz = $this->repository->update($data['id'], $data);
        $quiz->questions()->sync($questionIds);
        return $quiz;
    }



    /**
     * @throws Exception
     */
    public function updateIQ(Request $request): object|bool
    {

        $data = $request->validated();
        $questionItems = json_decode($data['selected_questions'] ?? '[]', true);
        $quiz = $this->repository->update($data['id'], $data);

        $syncData = [];
        foreach ($questionItems as $index => $questionId) {
            $syncData[(int) $questionId] = [
                'sequence' => $index
            ];
        }
        $quiz->questions()->sync($syncData);
        return $quiz;
    }

    /**
     * @throws Exception
     */
    public function delete($id): object
    {
        return $this->repository->delete($id);

    }

    public function actionMultipleRecords(Request $request): bool
    {
        $this->data = $request->all();

        switch ($this->data['action']) {
            case ActiveStatus::Active->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Active);
                }
                return true;
            case ActiveStatus::Draft->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Draft);
                }
                return true;
            case ActiveStatus::Deleted->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Deleted);
                }
                return true;

            default:
                return false;
        }
    }
}
