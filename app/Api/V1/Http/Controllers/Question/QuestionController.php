<?php

namespace App\Api\V1\Http\Controllers\Question;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Question\QuestionRequest;
use App\Api\V1\Http\Resources\Question\QuestionResourceCollection;
use App\Api\V1\Repositories\Question\QuestionRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use Exception;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;


/**
 * @group Câu hỏi
 */
class QuestionController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        QuestionRepositoryInterface $repository,

    )
    {
        $this->repository = $repository;

    }

    /**
     * DS Câu hỏi
     *
     * DS Câu hỏi
     *
     * kiểu câu hỏi (question_type) gồm:
     * - iq
     * - eq
     * -aq
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @queryParam page integer
     * Trang hiện tại, page > 0. Example: 1
     *
     * @queryParam limit integer
     * Số lượng thông báo trong 1 trang, limit > 0. Example: 1
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *    "status": 200,
     *    "message": "Thực hiện thành công.",
     *    "data": [
     *        {
     *               "id": 1,
     *              "age": 17,
     *              "question": "Câu hỏi 1"
     *        }
     *            ]
     * }
     *
     * @param QuestionRequest $request
     *
     * @return JsonResponse
     */
    public function index(QuestionRequest $request): JsonResponse
    {
        try {
            $response = $this->repository->index(
                $request->limit ?? 10,
                $request->page ?? 1,
                $request->question_type ?? ""
            );
            if ($response) {
                return $this->jsonResponseSuccess(new QuestionResourceCollection($response));
            } else {
                return $this->jsonResponseError('Get user Questions failed', 500);
            }

        } catch (Exception $e) {
            $this->logError('Get user Questions failed:', $e);
            return $this->jsonResponseError('Get user Questions failed', 500);
        }
    }


}
