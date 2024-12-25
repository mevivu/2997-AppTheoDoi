<?php

namespace App\Api\V1\Http\Controllers\Classes;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Resources\Classes\ClassesResource;
use App\Api\V1\Repositories\Classes\ClassesRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;


/**
 * @group lớp
 */
class ClassesController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        ClassesRepositoryInterface $repository,


    )
    {
        $this->repository = $repository;
    }

    public function index()
    {
        try {
            $response = $this->repository->findClassesActive();
            return $this->jsonResponseSuccess(ClassesResource::collection($response));
        } catch (Exception $e) {
            $this->logError('Get Classes List failed:', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh sách lớp.', 500);
        }
    }

}
