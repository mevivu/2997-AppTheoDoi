<?php

namespace App\Api\V1\Http\Controllers\Diaries;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\Diaries\DiariesRequest;
use App\Api\V1\Http\Requests\Diaries\DiariesUpdateRequest;
use App\Api\V1\Repositories\Diaries\DiariesRepositoryInterface;
use App\Api\V1\Services\Diaries\DiariesService;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Validate\Validator;
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
        DiariesService             $service

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
            if ($response) {
                return $this->jsonResponseSuccessNoData();
            } else {
                return $this->jsonResponseError('Get Diaries failed', 500);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->logError('Diaries  failed:', $exception);
            return $this->jsonResponseError('Get Diaries failed', 500);
        }

    }

    public function update(DiariesUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            $response = $this->service->update($request);
            DB::commit();
            if ($response) {
                return $this->jsonResponseSuccessNoData();
            } else {
                return $this->jsonResponseError('Failed to update Diary information', 500);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->logError('Diary Update failed:', $exception);
            return $this->jsonResponseError('Failed to update Diary information', 500);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            Validator::validateExists($this->repository, $id);
            $this->repository->delete($id);
            return $this->jsonResponseSuccess('Delete child successfully');
        } catch (BadRequestException|NotFoundException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (\Exception $e) {
            $this->logError('Delete child failed:', $e);
            return $this->jsonResponseError('Delete child failed', 500);
        }
    }
}
