<?php

namespace App\Api\V1\Http\Controllers\ChildEvaluation;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\ChildEvaluation\ChildEvaluationInfoRequest;
use App\Api\V1\Http\Requests\ChildEvaluation\ChildEvaluationRequest;
use App\Api\V1\Http\Resources\ChildEvaluation\ChildEvaluationInfoResource;
use App\Api\V1\Http\Resources\ChildEvaluation\ChildEvaluationResourceCollection;
use App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepositoryInterface;
use App\Api\V1\Repositories\ClassGrade\ClassGradeRepositoryInterface;
use App\Api\V1\Services\ChildEvaluation\ChildEvaluationServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Đánh giá năng lực
 */
class ChildEvaluationController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected ClassGradeRepositoryInterface $classGradeRepository;

    public function __construct(
        ChildEvaluationRepositoryInterface $repository,
        ClassGradeRepositoryInterface     $classGradeRepository,
        ChildEvaluationServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->classGradeRepository = $classGradeRepository;
        $this->service = $service;
        $this->middleware('auth:api');

    }

    /**
     * Lấy danh sách đánh giá năng lực theo ID của bảng điểm lớp.
     *
     * @authenticated
     * @queryParam class_id int required ID của lớp học. Example: 1
     * @queryParam semester string required Kỳ học. Example: semester_1
     * @queryParam child_id int required ID của trẻ cần lấy đánh giá. Example: 1
     *
     * @response 200 {
     *     "status": "success",
     *     "message": "Dữ liệu đánh giá năng lực được truy xuất thành công.",
     *     "data": [Danh sách đánh giá năng lực]
     * }
     * @response 404 {
     *     "status": "error",
     *     "message": "Không tìm thấy bản ghi."
     * }
     * @response 500 {
     *     "status": "error",
     *     "message": "Lỗi server nội bộ."
     * }
     *
     * @param ChildEvaluationInfoRequest $request
     * @return JsonResponse
     */
    public function findByClassGrade(ChildEvaluationInfoRequest $request): JsonResponse
    {
        try {
            $response = $this->service->findByClass($request);
            return $this->jsonResponseSuccess(new ChildEvaluationInfoResource($response));
        } catch (Exception $e) {
            $this->logError('findByClassGrade:', $e);
            return $this->jsonResponseError('Lỗi server nội bộ khi truy xuất đánh giá năng lực.', 500);
        }
    }

    /**
     * Tạo mới Đánh giá năng lực theo lớp và kì
     *
     * @authenticated
     * @bodyParam class_grade_id int required ID của lớp. Example: 1
     * @bodyParam semester string required Kỳ học. Example: semester_1
     * @bodyParam status string required Trạng thái của đánh giá. Example: draft
     * @bodyParam conduct string required Hạnh kiểm của học sinh. Example: good
     * @bodyParam academic_performance string required Học lực của học sinh. Example: excellent
     * @bodyParam subjects array required Mảng các môn học và điểm số.
     * @bodyParam qualities array required Mảng các phẩm chất.
     * @bodyParam capabilities array required Mảng các năng lực.
     *
     * @response 201 {
     *     "status": 201,
     *     "message": "Child evaluation successfully created.",
     *     "data": {
     *         "id": 12,
     *         "class_grade_id": 1,
     *         "semester": "semester_1",
     *         "status": "draft",
     *         "conduct": "good",
     *         "academic_performance": "excellent",
     *         "average_score": 8.333333333333334,
     *         "created_at": "2024-12-26T07:38:17.000000Z",
     *         "updated_at": "2024-12-26T07:38:17.000000Z"
     *     }
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "Invalid input data."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Server error."
     * }
     *
     * @param ChildEvaluationRequest $request
     * @return JsonResponse
     */

    public function store(ChildEvaluationRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->store($request);
            DB::commit();
            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            DB::rollBack();
            $this->logError('Child Store failed:', $exception);
            return $this->jsonResponseError('Get user notifications failed', 500);
        }

    }

    /**
     * Cập nhật Đánh giá năng lực cho một trẻ cụ thể.
     *
     * Phương thức này nhận dữ liệu từ body của request để cập nhật thông tin đánh giá năng lực.
     * Dữ liệu có thể bao gồm học lực, hạnh kiểm, điểm số của các môn học, phẩm chất, và năng lực.
     *
     * @authenticated
     * @bodyParam child_evaluation_id int required ID của Đánh giá năng lực cần cập nhật.
     * @bodyParam class_grade_id int required ID của lớp. Example: 1
     * @bodyParam semester string required Kỳ học. Example: semester_1
     * @bodyParam status string required Trạng thái của đánh giá. Example: draft
     * @bodyParam conduct string required Hạnh kiểm của học sinh. Example: good
     * @bodyParam academic_performance string required Học lực của học sinh. Example: excellent
     * @bodyParam subjects array required Mảng các môn học và điểm số. Example: [{'id': 1, 'grade': 9.5}]
     * @bodyParam qualities array required Mảng các phẩm chất. Example: [{'id': 1, 'quality_status': 'achieved'}]
     * @bodyParam capabilities array required Mảng các năng lực. Example: [{'id': 1, 'capability_status': 'developed'}]
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Đánh giá năng lực được cập nhật thành công.",
     *     "data": {
     *         "id": 12,
     *         "class_grade_id": 1,
     *         "semester": "semester_1",
     *         "status": "draft",
     *         "conduct": "good",
     *         "academic_performance": "excellent",
     *         "average_score": 8.333333333333334,
     *         "updated_at": "2024-12-26T07:38:17.000000Z"
     *     }
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "Dữ liệu nhập vào không hợp lệ."
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Đánh giá năng lực không tồn tại."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống."
     * }
     *
     * @param ChildEvaluationRequest $request
     * @return JsonResponse
     */
    public function update(ChildEvaluationRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->update($request);
            DB::commit();
            return $this->jsonResponseSuccess($response);
        } catch (Exception $e) {
            DB::rollBack();
            $this->logError('Update child evaluation failed:', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi cập nhật đánh giá năng lực', 500);
        }
    }


    /**
     * Lấy danh sách đánh giá năng lực theo ID của trẻ.
     *
     * Phương thức này truy xuất danh sách phân trang các đánh giá năng lực của một trẻ cụ thể,
     * cho phép lọc và phân trang để quản lý khối lượng dữ liệu.
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ cần lấy đánh giá. Example: 1
     * @queryParam page int optional Trang hiện tại của kết quả phân trang. Example: 2
     * @queryParam limit int optional Số lượng kết quả mỗi trang. Example: 20
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Lấy danh sách đánh giá năng lực của trẻ thành công.",
     *     "data": {
     *         "current_page": 1,
     *         "data": [
     *             {
     *                 "id": 12,
     *                 "class_grade": {
     *                     "id": 1,
     *                     "name": "Lớp 1"
     *                 },
     *                 "semester": "Học kỳ 1",
     *                 "average_score": 8.3,
     *                 "academic_performance": "Giỏi",
     *                 "conduct": "Xuất sắc",
     *                 "created_at": "2024-12-26T07:38:17.000000Z",
     *                 "updated_at": "2024-12-26T07:38:17.000000Z"
     *             }
     *         ],
     *         "first_page_url": "http://example.com/api/child-evaluations?page=1",
     *         "from": 1,
     *         "last_page": 1,
     *         "last_page_url": "http://example.com/api/child-evaluations?page=1",
     *         "next_page_url": null,
     *         "path": "http://example.com/api/child-evaluations",
     *         "per_page": 10,
     *         "prev_page_url": null,
     *         "to": 1,
     *         "total": 1
     *     }
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "Dữ liệu nhập vào không hợp lệ."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách đánh giá năng lực của trẻ."
     * }
     *
     * @param ChildEvaluationRequest $request
     * @return JsonResponse
     */
    public function index(ChildEvaluationRequest $request): JsonResponse
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess(new ChildEvaluationResourceCollection($response));
        } catch (Exception $exception) {
            $this->logError('Get Children List failed:', $exception);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh sách đứa trẻ.', 500);
        }
    }




}
