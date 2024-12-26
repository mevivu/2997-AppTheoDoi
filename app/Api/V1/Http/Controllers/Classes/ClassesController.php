<?php

namespace App\Api\V1\Http\Controllers\Classes;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Resources\Classes\ClassesResource;
use App\Api\V1\Http\Resources\Subject\SubjectResource;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Repositories\Classes\ClassesRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use App\Api\V1\Validate\Validator;
use Illuminate\Http\JsonResponse;


/**
 * @group lớp
 */
class ClassesController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        ClassesRepositoryInterface $repository,


    ) {
        $this->repository = $repository;
    }

    /**
     * Lấy danh sách lớp đang hoạt động
     *
     * @authenticated
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": {
     *         "id": 4,
     *         "name": "asd"
     *     }
     * }
     *
     * @response 400 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách lớp",
     *     "data":null
     * }
     *
     * @return JsonResponse
     */
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
    /**
     * Lấy danh sách môn học theo lớp
     *
     * @authenticated
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": {
     *         "id": 2,
     *         "name": "Toán",
     *         "status": "active"
     *     }
     * }
     * 
     * @response 400 {
     *     "status": 400,
     *     "message": "Resource with ID 16 not found.",
     *     "data": null
     * }
     * 
     * 
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách môn học của lớp.",
     *     "data": null
     * }
     *
     * @return JsonResponse
     */

    public function findSubjectsByClasses($id)
    {
        try {
            Validator::validateExists($this->repository, $id);
            $response = $this->repository->findSubjectsByClasses($id);
            return $this->jsonResponseSuccess(SubjectResource::collection($response));
        } catch (NotFoundException | BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $e) {
            $this->logError('Get Subjects List failed:', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh sách môn học của lớp.', 500);
        }
    }
}
