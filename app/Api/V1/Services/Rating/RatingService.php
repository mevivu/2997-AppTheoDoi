<?php

namespace App\Api\V1\Services\Rating;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Answer\AnswerRepositoryInterface;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\Rating\RatingRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Group\GroupType;
use App\Enums\Question\QuestionType;
use Exception;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


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
    protected ChildRepositoryInterface $childRepository;

    protected FileService $fileService;

    public function __construct(
        RatingRepositoryInterface $repository,
        AnswerRepositoryInterface $answerRepository,
        ChildRepositoryInterface  $childRepository,
        FileService               $fileService
    )
    {
        $this->repository = $repository;
        $this->answerRepository = $answerRepository;
        $this->childRepository = $childRepository;
        $this->fileService = $fileService;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $type = $data['type'];

        $query = $this->repository->getByQueryBuilder([
            'child_id' => request()->get('child_id'),
            'type' => $type,
        ]);
        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws Exception
     */
    public function storeIQ(Request $request): object
    {
        $data = $request->validated();
        $answers = $data['answers'] ?? [];
        $childId = $data['child_id'];
        $child = $this->childRepository->findOrFail($childId);
        $childName = $child->fullname;
        $type = QuestionType::IQ->value;
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
        $result = $totalCount > 0 ? "{$correctCount}/{$totalCount}" : "0/0";
        $data['score'] = $correctCount;
        $data['result'] = $result;
        $data['type'] = $type;
        $data['description'] = $this->getDescriptionByTypeAndScore($type, $correctCount);
        $description = "Đã xuất sắc nhận được chứng chỉ trực tuyến bằng\n cách hoàn thành bài kiểm tra IQ năng cao.Điểm IQ\n đã được xác định bởi Bài kiểm tra IQ năng cao của\n CHAMCON360.";
        $path = $this->createCertificate($childName, $result, $description, now());
        $data['badge_image'] = $path;
        return $this->repository->create($data);
    }

    public function createCertificate($name, $score, $description, $date): string
    {
        $manager = new ImageManager(new Driver());

        $img = $manager->read(public_path('assets/images/certificate_template.png'));
        $width = $img->width();
        $height = $img->height();
        $fontLight = public_path('assets/fonts/Roboto-Light.ttf');
        $fontBold = public_path('assets/fonts/Roboto-Bold.ttf');


        $x = $width / 2;
        $yName = $height * 0.39;
        $yScore = $height * 0.46;
        $yDesc = $height * 0.62;
        $yDate = $height * 0.82;
        $xDate = $width / 3;

        $img->text($name, $x, $yName, function ($font) use ($fontBold) {
            $font->file($fontBold);
            $font->size(24);
            $font->color('#000');
            $font->align('center');
            $font->valign('middle');
        });

        $img->text("Điểm: " . $score, $x, $yScore, function ($font) use ($fontBold) {
            $font->file($fontBold);
            $font->size(20);
            $font->color('#32CD32');
            $font->align('center');
            $font->valign('middle');
        });

        $img->text($description, $x, $yDesc, function ($font) use ($fontLight) {
            $font->file($fontLight);
            $font->size(18);
            $font->color('#000');
            $font->align('center');
            $font->valign('middle');
            $font->lineHeight(1.9);
        });

        $img->text("Date: " . $date, $xDate, $yDate, function ($font) use ($fontLight) {
            $font->file($fontLight);
            $font->size(16);
            $font->color('#000');
            $font->align('center');
            $font->valign('middle');
        });

        $newFilename = uniqid() . '-certificate.jpg';
        $newImagePath = public_path('uploads/images/certificates/' . $newFilename);
        $path = '/public/uploads/images/certificates/' . $newFilename;
        $img->save($newImagePath, 90, 'jpg');

        return $path;
    }


    /**
     * @throws Exception
     */
    public function storeEQAndAQ(Request $request): object
    {
        $data = $request->validated();
        $answers = $data['answers'] ?? [];
        $type = $data['type'];
        $totalCount = count($answers) * 2;

        $scoreData = $this->calculateScores($answers, $totalCount);

        $data = array_merge($data, $scoreData);

        $type = $type == QuestionType::AQ->value ? QuestionType::AQ->value : QuestionType::EQ->value;
        $data['description'] = $this->getDescriptionByTypeAndScore($type, $data['score']);

        return $this->repository->create($data);
    }


    /**
     * @throws Exception
     */
    protected function calculateScores(array $answers, int $totalCount): array
    {
        $totalScore = 0;
        $data = [];

        foreach ($answers as $answer) {
            $answer = $this->answerRepository->findOrFail($answer['answer_id']);
            $question = $answer->question;
            $groupType = $question->group->type;
            $score = $answer->score * 2;
            $totalScore += $score;

            switch ($groupType) {
                case GroupType::Empathy:
                    $data['self_regulation'] = $totalCount > 0 ? "{$score}/{$totalCount}" : "0/0";
                    break;
                case GroupType::Motivation:
                    $data['social_awareness'] = $totalCount > 0 ? "{$score}/{$totalCount}" : "0/0";
                    break;
                case GroupType::SocialSkills:
                    $data['relationship_management'] = $totalCount > 0 ? "{$score}/{$totalCount}" : "0/0";
                    break;
                case GroupType::EmotionalRegulation:
                    $data['decision_making'] = $totalCount > 0 ? "{$score}/{$totalCount}" : "0/0";
                    break;
                case GroupType::EmotionalAwareness:
                    $data['optimism'] = $totalCount > 0 ? "{$score}/{$totalCount}" : "0/0";
                    break;
                default:
                    break;
            }
        }

        $data['score'] = $totalScore / 2.5;
        return $data;
    }


    protected function getDescriptionByTypeAndScore($type, $score)
    {
        $descriptions = [
            QuestionType::AQ->value => [
                5 => 'Miễn cưỡng hoặc không sẵn lòng đối mặt với khó khăn',
                7 => 'Tiêu cực, đề bỏ cuộc',
                7.6 => 'Tích cực nhưng cần hỗ trợ',
                8 => 'Tích cực, tự lực và có sự cố gắng',
                9.5 => 'Rất tích cực, kiên trì, vượt khó tốt'
            ],
            QuestionType::IQ->value => [
                3 => 'Tiêu cực, khó kiểm soát cảm xúc',
                5 => 'Tiêu cực, nhưng không thể hiện ra ngoài',
                7 => 'Trung tính, có cố gắng kiểm soát nhưng chưa hoàn toàn tự tin',
                9 => 'Tích cực, biết cách kiểm soát và xử lý tình huống',
                '>9' => 'Rất tích cực, dễ dàng kiểm soát cảm xúc và giúp người khác'
            ],
            QuestionType::EQ->value => [
                5 => 'Tiêu cực, nhưng không thể hiện ra ngoài',
                7 => 'Trung tính, có cố gắng kiểm soát nhưng chưa hoàn toàn tự tin',
                7.5 => 'Tích cực, biết cách kiểm soát và xử lý tình huống',
                8 => 'Tích cực, biết cách kiểm soát và xử lý tình huống',
                9 => 'Rất tích cực, dễ dàng kiểm soát cảm xúc và giúp người khác',
                9.3 => 'Rất tích cực, dễ dàng kiểm soát cảm xúc và giúp người khác'
            ]
        ];

        $lastDesc = "Chưa đánh giá được";
        foreach ($descriptions[$type] as $threshold => $desc) {
            if ($score >= $threshold) {
                $lastDesc = $desc;
            }
        }

        return $lastDesc;
    }


    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $response = $this->repository->findOrFail($id);
        $this->repository->delete($id);

    }


}
