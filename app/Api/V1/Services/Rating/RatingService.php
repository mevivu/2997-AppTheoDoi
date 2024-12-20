<?php

namespace App\Api\V1\Services\Rating;


use App\Api\V1\Repositories\Answer\AnswerRepositoryInterface;
use App\Api\V1\Repositories\Rating\RatingRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Question\QuestionType;
use Exception;
use Illuminate\Http\Request;


class RatingService implements RatingServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected RatingRepositoryInterface $repository;

    protected AnswerRepositoryInterface $answerRepository;


    public function __construct(
        RatingRepositoryInterface $repository,
        AnswerRepositoryInterface $answerRepository,
    )
    {
        $this->repository = $repository;
        $this->answerRepository = $answerRepository;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $type = $data['type'];

        $query = $this->repository->getByQueryBuilder([
            'child_id' => $data['child_id'],
            'type' => $type,
        ]);
        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $answers = $data['answers'] ?? [];
        $type = $data['type'];
        $correctCount = 0;
        $totalCount = count($answers);
        foreach ($answers as $answer) {
            $correct = $this->answerRepository->getByQueryBuilder(
                [
                    'id' => $answer['answer_id'],
                    'question_id' => $answer['question_id'],
                    'is_correct' => true
                ],
                ['child']
            )->exists();
            if ($correct) {
                $correctCount++;
            }
        }
        $score = $totalCount > 0 ? "{$correctCount}/{$totalCount}" : "0/0";
        $data['score'] = $score;
        $data['description'] = $this->getDescriptionByTypeAndScore($type, $correctCount);

        return $this->repository->create($data);
    }

    protected function getDescriptionByTypeAndScore($type, $score)
    {
        $descriptions = [
            QuestionType::AQ->value => [
                1 => 'Miễn cưỡng hoặc không sẵn lòng đối mặt với khó khăn',
                5 => 'Tiêu cực, đề bỏ cuộc',
                7 => 'Tích cực nhưng cần hỗ trợ',
                9 => 'Tích cực, tự lực và có sự cố gắng',
                10 => 'Rất tích cực, kiên trì, vượt khó tốt'
            ],
            QuestionType::IQ->value => [
                3 => 'Tiêu cực, khó kiểm soát cảm xúc',
                5 => 'Tiêu cực, nhưng không thể hiện ra ngoài',
                7 => 'Trung tính, có cố gắng kiểm soát nhưng chưa hoàn toàn tự tin',
                9 => 'Tích cực, biết cách kiểm soát và xử lý tình huống',
                '>9' => 'Rất tích cực, dễ dàng kiểm soát cảm xúc và giúp người khác'
            ],
        ];

        foreach ($descriptions[$type] as $threshold => $desc) {
            if ($score >= (int)$threshold) {
                return $desc;
            }
        }

        return "oke";
    }

    /**
     * @throws Exception
     */


    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $response = $this->repository->findOrFail($id);
        $this->repository->delete($id);

    }
}
