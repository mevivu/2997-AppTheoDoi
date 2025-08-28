<?php

namespace App\Api\V1\Services\Rating;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Answer\AnswerRepositoryInterface;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\Quiz\QuizRepositoryInterface;
use App\Api\V1\Repositories\Rating\RatingRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Group\GroupType;
use App\Enums\Question\QuestionType;
use App\Enums\VerifiedStatus;
use App\Models\Quiz;
use Exception;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


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
    protected QuizRepositoryInterface $quizRepository;

    protected FileService $fileService;

    public function __construct(
        RatingRepositoryInterface $repository,
        AnswerRepositoryInterface $answerRepository,
        ChildRepositoryInterface  $childRepository,
        FileService               $fileService,
        QuizRepositoryInterface   $quizRepository
    )
    {
        $this->repository = $repository;
        $this->answerRepository = $answerRepository;
        $this->childRepository = $childRepository;
        $this->fileService = $fileService;
        $this->quizRepository = $quizRepository;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $type = $data['type'];

        $query = $this->repository->getQueryBuilder();
        $query->where('child_id', $data['child_id']);
        $query->where('type', $type);

        if ($type === QuestionType::IQ->value) {
            $query->orderBy('age','asc');
        }
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
        $quizId = $data['quiz_id'];
        $quiz = $this->quizRepository->findOrFail($quizId);
        $totalCount = $quiz->questions()->count();
        $ratingId = $data['rating_id'];
        $child = $this->childRepository->findOrFail($childId);
        $childName = $child->fullname;
        $type = QuestionType::IQ->value;
        $correctCount = 0;
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
        $scoreValue = $correctCount * 10;
        $totalValue = $totalCount * 10;
        $result = $totalCount > 0 ? "{$scoreValue}/{$totalValue}" : "0/0";
        $data['score'] = min(10, floor($correctCount / 1.5));
        $data['result'] = $result;
        $data['type'] = $type;
        $data['description'] = $this->getDescriptionByTypeAndScore($type, $correctCount);
        $data['label'] = $this->getLabelByTypeAndScore($type, $correctCount);
        $description = "Đã xuất sắc nhận được kết quả đánh giá trực tuyến\nbằng cách hoàn thành Bài kiểm tra IQ nâng cao của\nCHAMCON360.";
        $path = $this->createCertificate($childName, $result, $description, now());
        $data['badge_image'] = $path;
        $data['status'] = VerifiedStatus::Active;
        return $this->repository->update($ratingId, $data);
    }

    public function createCertificate($name, $score, $description, $date): string
    {
        $manager = new ImageManager(new Driver());

        $img = $manager->read(public_path('assets/images/certificate_template.png'));
        $width = $img->width();
        $height = $img->height();


        $fontLight = public_path('assets/fonts/Roboto-Light.ttf');
        $fontBold = public_path('assets/fonts/Roboto-Bold.ttf');


        $xCenter = $width / 2;
        $xScore = $width * 0.80;
        $yScore = $height * 0.31;
        $yName = $height * 0.42;
        $yDesc = $height * 0.59;
        $yDate = $height * 0.74;
        $xDate = $width / 3;

        $img->text($score, $xScore, $yScore, function ($font) use ($fontBold) {
            $font->file($fontBold);
            $font->size(24);
            $font->color('#FF0000');
            $font->align('center');
            $font->valign('middle');
        });


        $img->text($name, $xCenter, $yName, function ($font) use ($fontBold) {
            $font->file($fontBold);
            $font->size(24);
            $font->color('#000');
            $font->align('center');
            $font->valign('middle');
        });

        $img->text($description, $xCenter, $yDesc, function ($font) use ($fontLight) {
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

        $footerText = "Đây là phiếu ghi nhận kết quả mang tính tham khảo, không phải chứng chỉ hay văn bằng có giá trị pháp lý.";
        $yFooter = $height * 0.87;

        $img->text($footerText, $xCenter, $yFooter, function ($font) use ($fontLight) {
            $font->file($fontLight);
            $font->size(10);
            $font->color('#333333');
            $font->align('center');
            $font->valign('bottom');
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
        $data['label'] = $this->getLabelByTypeAndScore($type, $data['score']);
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
                    $data['self_regulation'] = $totalCount > 0 ? "{$score}" : "0";
                    break;
                case GroupType::Motivation:
                    $data['social_awareness'] = $totalCount > 0 ? "{$score}" : "0";
                    break;
                case GroupType::SocialSkills:
                    $data['relationship_management'] = $totalCount > 0 ? "{$score}" : "0";
                    break;
                case GroupType::EmotionalRegulation:
                    $data['decision_making'] = $totalCount > 0 ? "{$score}" : "0";
                    break;
                case GroupType::EmotionalAwareness:
                    $data['optimism'] = $totalCount > 0 ? "{$score}" : "0";
                    break;
                case GroupType::Tolerance:
                    $data['endurance'] = $totalCount > 0 ? "{$score}" : "0";
                    break;
                case GroupType::Flexibility:
                    $data['flexibility'] = $totalCount > 0 ? "{$score}" : "0";
                    break;
                case GroupType::Perseverance:
                    $data['perseverance'] = $totalCount > 0 ? "{$score}" : "0";
                    break;
                case GroupType::Positivity:
                    $data['positivity'] = $totalCount > 0 ? "{$score}" : "0";
                    break;
                case GroupType::SelfReflection:
                    $data['self_reflection'] = $totalCount > 0 ? "{$score}" : "0";
                    break;
                default:
                    break;
            }
        }

        $data['score'] = ($totalScore / 2.5) /2;
        return $data;
    }

    protected function getLabelByTypeAndScore($type, $score)
    {
        $descriptions = [
            QuestionType::IQ->value => [
                0 => 'Kém',
                5 => 'Trung bình',
                7 => 'Khá cao',
                8 => 'Rất cao',
                9 => 'Xuất sắc'
            ],
            QuestionType::EQ->value => [
                0 => 'Tiêu cực',
                3 => 'Tiêu cực',
                5 => ' Tiêu cực ẩn',
                7 => 'Trung tính',
                9 => 'Tích cực',
                9.1 => 'Rất tích cực'
            ],
            QuestionType::AQ->value => [
                0 => 'Quiter',
                3 => 'Quiter',
                5 => 'Quiter',
                7 => 'Camper',
                9 => 'Climber',
                9.1 => 'Climber'
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

    protected function getDescriptionByTypeAndScore($type, $score)
    {
        $descriptions = [
            QuestionType::AQ->value => [
                0 => 'Tiêu cực, dễ bỏ cuộc.',
                3 => 'Tiêu cực, dễ bỏ cuộc.',
                5 => 'Miễn cưỡng hoặc không sẵn lòng đối mặt với khó khăn.',
                7 => 'Tích cực nhưng có thể cần hỗ trợ.',
                9 => 'Tích cực, tự lực và có sự cố gắng.',
                9.1 => 'Rất tích cực, kiên trì, vượt khó tốt'
            ],
            QuestionType::IQ->value => [
                0 => 'Kém',
                5 => 'Trung bình',
                7 => 'Khá cao',
                8 => 'Rất cao',
                9 => 'Xuất sắc'
            ],
            QuestionType::EQ->value => [
                0 => 'Tiêu cực, khó kiểm soát cảm xúc.',
                3 => 'Tiêu cực, khó kiểm soát cảm xúc.',
                5 => 'Tiêu cực, nhưng không thể hiện ra ngoài',
                7 => 'Trung tính, có cố gắng kiểm soát nhưng chưa hoàn toàn tự tin.',
                9 => 'Tích cực, biết cách kiểm soát và xử lý tình huống.',
                9.1 => 'Rất tích cực, dễ dàng kiểm soát cảm xúc và giúp người khác'
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
