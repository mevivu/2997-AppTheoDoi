<?php

namespace App\Api\V1\Http\Controllers\Rating;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Rating\StoreIqV2Request;
use App\Api\V1\Http\Resources\Rating\RatingResource;
use App\Api\V1\Services\Rating\RatingServiceV2Interface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Traits\MessageSystem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Đánh giá IQ V2 (Tích hợp Memo Game)
 */
class RatingV2Controller extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(RatingServiceV2Interface $service)
    {
        $this->service = $service;
        $this->middleware('auth:api');
    }

    /**
     * Submit kết quả bài kiểm tra IQ V2 (12 câu hỏi + Memo Game)
     *
     * @authenticated
     */
    public function storeIQV2(StoreIqV2Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->storeIQV2($request);
            DB::commit();
            return $this->jsonResponseSuccess(new RatingResource($response));
        } catch (Exception $exception) {
            DB::rollBack();
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }
}
