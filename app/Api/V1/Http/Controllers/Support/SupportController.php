<?php

namespace App\Api\V1\Http\Controllers\Support;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Support\SupportRequest;
use App\Api\V1\Http\Resources\Support\SupportResource;
use App\Api\V1\Http\Resources\Support\SupportResourceCollection;
use App\Api\V1\Repositories\Support\SupportRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Enums\ActiveStatus;
use Illuminate\Http\JsonResponse;
use App\Api\V1\Validate\Validator;

/**
 * @group Hỗ trợ khách hàng
 */
class SupportController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        SupportRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    /**
     * Danh sách hỗ trợ
     *
     * `type`: Loại hỗ trợ bao gồm:
     *      - `help_center`: Trung tâm trợ giúp
     *      - `guide`: Hướng dẫn sử dụng
     *
     * @param \App\Api\V1\Http\Requests\Support\SupportRequest $request
     * @return void
     */
    public function index(SupportRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $type = $data['type'];
            $page = $data['page'] ?? 1;
            $limit = $data['limit'] ?? 10;

            $supports = $this->repository->getByQueryBuilder([
                'type' => $type,
                'status' => ActiveStatus::Active->value,
            ])->paginate($limit, ['*'], 'page', $page);

            return $this->jsonResponseSuccess(new SupportResourceCollection($supports));
        } catch (\Exception $e) {
            $this->logError('Get list support failed', $e);
            return $this->jsonResponseError('Get list support failed', 500);
        }
    }

    /**
     * Chi tiết hỗ trợ
     *
     * @param $id
     * @return void
     */

    public function show($id): JsonResponse
    {
        try {
            $support = $this->repository->find($id);
            if (!$support) {
                return $this->jsonResponseError('Không tìm thấy thông tin', 404);
            }
            return $this->jsonResponseSuccess(new SupportResource($support));
        } catch (\Exception $e) {
            $this->logError('Get detail support failed', $e);
            return $this->jsonResponseError('Get detail support failed', 500);
        }
    }
}