<?php

namespace App\Api\V1\Http\Controllers\Transaction;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\Transaction\TransactionRequest;
use App\Api\V1\Http\Resources\Transaction\TransactionResource;
use App\Api\V1\Http\Resources\Transaction\TransactionResourceCollection;
use App\Api\V1\Repositories\Transaction\TransactionRepositoryInterface;
use App\Api\V1\Services\Transaction\TransactionServiceInterface;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Validate\Validator;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * @group Thông tin giao dịch
 */
class TransactionController extends Controller
{
    use Response, UseLog;

    public function __construct(TransactionRepositoryInterface $repository,
                                TransactionServiceInterface    $service)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->middleware('auth:api');
    }

    /**
     * Lấy danh sách giao dịch theo người dùng
     *
     * @authenticated
     *
     * @queryParam page integer nullable Example: 1
     * @queryParam limit integer nullable Example: 1
     *
     * @response 200 {
     *       "status": 200,
     *       "message": "Thực hiện thành công.",
     *       "data": {
     *           "current_page": 1,
     *           "data": [
     *               {
     *                   "id": 2,
     *                   "wallet_id": 1,
     *                   "type": "deposit",
     *                   "amount": "12100000",
     *                   "code": "TRX_66cec1fad0ced",
     *                   "created_at": "2024-08-28T06:21:46.000000Z",
     *                   "updated_at": "2024-08-28T06:21:46.000000Z"
     *               },
     *               {
     *                   "id": 1,
     *                   "wallet_id": 1,
     *                   "type": "deposit",
     *                   "amount": "20000",
     *                   "code": "TRX_66ceaac2ba03e",
     *                   "created_at": "2024-08-28T04:42:42.000000Z",
     *                   "updated_at": "2024-08-28T04:42:42.000000Z"
     *               }
     *           ],
     *           "first_page_url": "http://localhost:8080/2792-SEE/api/v1/transactions?page=1",
     *           "from": 1,
     *           "last_page": 1,
     *           "last_page_url": "http://localhost:8080/2792-SEE/api/v1/transactions?page=1",
     *           "links": [
     *               {
     *                   "url": null,
     *                   "label": "&laquo; Previous",
     *                   "active": false
     *               },
     *               {
     *                   "url": "http://localhost:8080/2792-SEE/api/v1/transactions?page=1",
     *                   "label": "1",
     *                   "active": true
     *               },
     *               {
     *                   "url": null,
     *                   "label": "Next &raquo;",
     *                   "active": false
     *               }
     *           ],
     *           "next_page_url": null,
     *           "path": "http://localhost:8080/2792-SEE/api/v1/transactions",
     *           "per_page": 10,
     *           "prev_page_url": null,
     *           "to": 2,
     *           "total": 2
     *       }
     *   }
     *
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống."
     * }
     *
     * @param TransactionRequest $request Đối tượng request chứa dữ liệu validation cho việc kiểm tra danh sách giao dịch theo Loại giao dịch lọc theo      * page , limit .
     * @return JsonResponse Phản hồi JSON chứa kết quả kiểm tra.
     */

    public function index(TransactionRequest $request): JsonResponse
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess(new TransactionResourceCollection($response));
        } catch (Throwable $e) {
            $this->logError('User creation failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    /**
     * Chi tiết
     *
     * @pathParam id integer required
     *
     * @response 200 {
     *    "status": 200,
     *    "message": "Thực hiện thành công.",
     *    "data": [
     *        {
     *                   "id": 3,
     *                  "title": "1",
     *                  "message": "1",
     *                  "status": 2,
     *                  "read_at": "13-12-2024 14:07",
     *                   "created_at": "30-10-2024 16:07"
     *        }
     *
     *    ]
     * }
     * @response 500 {
     *          "status": 500,
     *         "message": "Get user notifications detail failed",
     *  }
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            Validator::validateExists($this->repository, $id);
            $response = $this->repository->findOrFail($id);
            return $this->jsonResponseSuccess(new TransactionResource($response));
        } catch (NotFoundException|BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $exception) {
            $this->logError('Get detail Exercises failed:', $exception);
            return $this->jsonResponseError('Get detail Exercises failed', 500);
        }
    }


}
