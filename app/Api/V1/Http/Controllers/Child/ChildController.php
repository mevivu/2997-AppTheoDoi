<?php

namespace App\Api\V1\Http\Controllers\Child;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Child\ChildRequest;
use App\Api\V1\Services\Child\ChildServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;

/**
 * @group Con
 */
class ChildController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        ChildServiceInterface $service

    )
    {
        $this->service = $service;
        $this->middleware('auth:api');

    }

    public function store(ChildRequest $request): JsonResponse
    {
        try {
            $response = $this->service->store($request);
            return $this->jsonResponseSuccess($response);
        } catch (\Exception $exception) {
            $this->logError('Child Store failed:', $exception);
            return $this->jsonResponseError('Get user notifications failed', 500);
        }

    }


}
