<?php

namespace App\Api\V1\Http\Controllers\Quiz;

use App\Api\V1\Http\Requests\Quiz\QuizAQAndEQRequest;
use App\Api\V1\Http\Requests\Quiz\QuizEQAndAQRequest;
use App\Api\V1\Http\Resources\Question\QuestionResource;
use App\Api\V1\Http\Resources\Quiz\QuizEQAndAQResource;
use App\Api\V1\Repositories\Quiz\QuizRepositoryInterface;
use App\Api\V1\Services\Quiz\QuizServiceInterface;
use App\Api\V1\Http\Resources\Quiz\QuizIQResource;
use App\Api\V1\Http\Requests\Quiz\QuizIQRequest;
use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Support\AuthServiceApi;
use Illuminate\Http\JsonResponse;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;

/**
 * @group Bài kiểm tra
 */
class QuizController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        QuizRepositoryInterface $repository,
        QuizServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');
    }

    /**
     * Lấy danh sách bài kiểm tra có type IQ và tuổi
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
     * @param QuizIQRequest $request
     * @return JsonResponse
     */

    public function getListIQ(QuizIQRequest $request): JsonResponse
    {
        try {
            $response = $this->service->getListIQ($request);
            return $this->jsonResponseSuccess(new QuizIQResource($response));
        } catch (Exception $exception) {
            $this->logError('Lỗi hệ thống khi lấy danh sách bài kiểm tra:', $exception);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh sách bài kiểm tra IQ', 500);
        }
    }

    /**
     * Lấy danh sách bài kiểm tra AQ,EQ
     *
     *
     * @authenticated
     * @queryParam type string required Loại. Example: aq
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
     *                     "question": "Which behavior is best in social situations?",
     *                     "question_type": "aq",
     *                     "answers": [
     *                         {
     *                             "answer_id": 25,
     *                             "answer": "Sharing toys"
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
     *     "message": "Lỗi hệ thống khi lấy danh sách bài kiểm tra AQ."
     * }
     *
     * @param QuizAQAndEQRequest $request
     * @return JsonResponse
     */
    public function getListAQAndEQ(QuizAQAndEQRequest $request): JsonResponse
    {
        try {
            $response = $this->service->getListAQAndEQ($request);
            return $this->jsonResponseSuccess(new QuizEQAndAQResource($response));
        } catch (Exception $exception) {
            $this->logError('Lỗi hệ thống khi lấy danh sách bài kiểm tra AQ,EQ:', $exception);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh sách bài kiểm tra AQ,EQ', 500);
        }
    }

    /**
     * Lấy danh sách câu hỏi EQ,AQ ngẫu nhiên
     *
     * Thông tin trả về gồm câu hỏi (questions) và câu trả lời (answers)
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     * @queryParam type string required Loại. Example: aq
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": [
     *         {
     *             "question_id": 6,
     *             "question": "Which behavior is best in social situations?",
     *             "question_type": "eq",
     *             "answers": [
     *                 {
     *                     "answer_id": 25,
     *                     "answer": "Sharing toys"
     *                 }
     *             ]
     *         }
     *     ]
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách câu hỏi EQ."
     * }
     *
     * @param QuizEQAndAQRequest $request
     * @return JsonResponse
     */
    public function getRandomEQ(QuizEQAndAQRequest $request): JsonResponse
    {
        try {
            $response = $this->service->getRandomEQAQ($request);
            return $this->jsonResponseSuccess(QuestionResource::collection($response));
        } catch (Exception $exception) {
            $this->logError('Lỗi hệ thống khi lấy danh sách câu hỏi EQ:', $exception);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh sách câu hỏi EQ', 500);
        }
    }
}
