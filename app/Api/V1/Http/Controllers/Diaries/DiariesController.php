<?php

namespace App\Api\V1\Http\Controllers\Diaries;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Child\ChildRequest;
use App\Api\V1\Http\Requests\Diaries\DiariesRequest;
use App\Api\V1\Http\Resources\Child\ChildResource;
use App\Api\V1\Repositories\Diaries\DiariesRepositoryInterface;
use App\Api\V1\Services\Diaries\DiariesService;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


/**
 * @group Toa thuoc
 */
class DiariesController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
       DiariesRepositoryInterface $repository,
        DiariesService    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function store(DiariesRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->store($request);
            DB::commit();
            return $this->jsonResponseSuccessNoData();
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->logError('Diaries  failed:', $exception);
            return $this->jsonResponseError('Get Diaries failed', 500);
        }

    }
}
