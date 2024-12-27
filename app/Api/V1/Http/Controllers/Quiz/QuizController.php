<?php

namespace App\Api\V1\Http\Controllers\Quiz;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\Pregnancy\PregnancyRequest;
use App\Api\V1\Http\Requests\Pregnancy\PregnancyUpdateRequest;
use App\Api\V1\Http\Requests\Quiz\QuizRequest;
use App\Api\V1\Http\Resources\Pregnancy\PregnancyCollection;
use App\Api\V1\Http\Resources\Pregnancy\PregnancyResource;
use App\Api\V1\Repositories\Quiz\QuizRepositoryInterface;
use App\Api\V1\Services\Quiz\QuizServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Validate\Validator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Quiz;
use App\Api\V1\Http\Resources\Quiz\QuizResource;
use Illuminate\Http\Request;

/**
 * @group Bài kiểm tra
 */
class QuizController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        QuizRepositoryInterface $repository,
        QuizServiceInterface    $service

    ) {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');
    }
    /**
     * Lấy danh sách bài kiểm tra có type AQ và tuổi
     *
     * Thông tin trả về gồm câu hỏi (questions) và câu trả lời (answers)
     *
     *
     * @authenticated
     * @queryParam age int required tuổi của trẻ. Example: 1
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": [
     *         {
     *             "quiz_id": 9,
     *             "age": 5,
     *             "type": "aq",
     *             "questions": [
     *                 {
     *                     "question_id": 6,
     *                     "question": "Look at this series: 31, 29, 24, 22, 17, ... What number should come next?",
     *                     "question_type": "aq",
     *                     "answers": [
     *                         {
     *                             "answer_id": 25,
     *                             "answer": "12"
     *                         }
     *                     ]
     *                 }
     *             ]
     *         }
     *     ]
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách bài kiểm tra."
     * }
     *
     * @param QuizRequest $request
     * @return JsonResponse
     */

    public function index(QuizRequest $request)
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess(new QuizResource($response));
        } catch (Exception $exception) {
            $this->logError('Lỗi hệ thống khi lấy danh sách bài kiểm tra:', $exception);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh sách bài kiểm tra', 500);
        }
    }
}
