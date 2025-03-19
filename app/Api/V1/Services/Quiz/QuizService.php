<?php

namespace App\Api\V1\Services\Quiz;


use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\Question\QuestionRepositoryInterface;
use App\Api\V1\Repositories\Quiz\QuizRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\ActiveStatus;
use App\Enums\Question\AgeGroup;
use Illuminate\Http\Request;
use App\Enums\Question\QuestionType;

class QuizService implements QuizServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected QuizRepositoryInterface $repository;

    protected ChildRepositoryInterface $childRepository;

    protected QuestionRepositoryInterface $questionRepository;



    public function __construct(
        QuizRepositoryInterface $repository,
        ChildRepositoryInterface $childRepository,
        QuestionRepositoryInterface $questionRepository
    )
    {
        $this->repository = $repository;
        $this->childRepository = $childRepository;
        $this->questionRepository = $questionRepository;
    }

    public function getListIQ(Request $request)
    {
        $data = $request->validated();
        $age = $data['age'];
        $response = $this->repository->getByQueryBuilder(
            [
                'age' => $age,
                'type' => QuestionType::IQ
            ]
        );
        return $response->get();
    }

    public function getListAQAndEQ(Request $request)
    {
        $data = $request->validated();
        $type = $data['type'];
        $response = $this->repository->getByQueryBuilder(
            [
                'type' => $type
            ]
        );
        return $response->get();
    }

    /**
     * @throws \Exception
     */
    public function getRandomEQAQ(Request $request)
    {
        $data = $request->validated();
        $childId = $data['child_id'];
        $type = $data['type'];
        $child = $this->childRepository->findOrFail($childId);
        $age = $child->age;

        // Determine the age group dynamically based on the child's age
        $ageGroup = $age < 10 ? AgeGroup::Under_10 : AgeGroup::Above_10;

        // Fetch questions for the appropriate age group
        $questions = $this->questionRepository->getQueryBuilder()
            ->where('age_group', $ageGroup)
            ->where('question_type', $type)
            ->where('status', ActiveStatus::Active)
            ->get();

        // Group the questions by `question_group_id`, select a random question from each group,
        // reset the keys, and then randomize the order of the questions
        $randomQuestions = $questions->groupBy('question_group_id')
            ->map(function ($groupQuestions) {
                return $groupQuestions->random();
            })
            ->values();  // Reset the keys to sequential order

        // Randomize the order of the questions after selection
        $randomQuestions = $randomQuestions->shuffle();

        return $randomQuestions;
    }



}
