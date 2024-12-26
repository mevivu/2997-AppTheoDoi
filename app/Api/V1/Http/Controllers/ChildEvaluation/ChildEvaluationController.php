<?php

namespace App\Api\V1\Http\Controllers\ChildEvaluation;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\ChildEvaluation\ChildEvaluationDetailRequest;
use App\Api\V1\Http\Requests\ChildEvaluation\ChildEvaluationRequest;
use App\Api\V1\Http\Requests\ChildEvaluation\ChildEvaluationSearchRequest;
use App\Api\V1\Http\Resources\ChildEvaluation\ChildEvaluationDetailResource;
use App\Api\V1\Http\Resources\ChildEvaluation\ChildEvaluationResourceCollection;
use App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepositoryInterface;
use App\Api\V1\Services\ChildEvaluation\ChildEvaluationServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Validate\Validator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Đánh giá năng lực
 */
class ChildEvaluationController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        ChildEvaluationRepositoryInterface $repository,
        ChildEvaluationServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');

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

    /**
     * Lấy chi tiết đánh giá năng lực của một trẻ.
     *
     * Phương thức này trả về chi tiết đánh giá năng lực của một trẻ cụ thể bằng cách sử dụng ID đánh giá.
     *
     * @authenticated
     * @urlParam evaluation_id int required ID của đánh giá năng lực cần lấy chi tiết. Example: 1
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Chi tiết đánh giá năng lực của trẻ được lấy thành công.",
     *     "data": {
     *         "id": 12,
     *         "child_id": 1,
     *         "class_grade": {
     *             "id": 1,
     *             "name": "Lớp 1"
     *         },
     *         "semester": "Học kỳ 1",
     *         "average_score": 8.3,
     *         "academic_performance": "Giỏi",
     *         "conduct": "Xuất sắc",
     *         "created_at": "2024-12-26T07:38:17.000000Z",
     *         "updated_at": "2024-12-26T07:38:17.000000Z"
     *     }
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
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            Validator::validateExists($this->repository, $id);
            $response = $this->service->show($id);
            return $this->jsonResponseSuccess(new ChildEvaluationDetailResource($response));
        } catch (BadRequestException|NotFoundException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $e) {
            $this->logError('Show detail child failed:', $e);
            return $this->jsonResponseError('Show detail child failed', 500);
        }
    }

    /**
     * Tìm kiếm đánh giá năng lực của trẻ dựa vào ID lớp học, ID bảng điểm lớp và kỳ học.
     *
     * Phương thức này nhận các tham số từ query để tìm kiếm thông tin đánh giá năng lực của trẻ phù hợp với điều kiện đã cho.
     * Nếu tìm thấy thông tin đánh giá, nó sẽ trả về chi tiết thông tin đó. Nếu không tìm thấy, trả về mảng rỗng.
     *
     * @authenticated
     * @queryParam class_id int required ID của lớp học cần tìm kiếm. Example: 1
     * @queryParam class_grade_id int required ID của bảng điểm lớp cần tìm kiếm. Example: 1
     * @queryParam semester string required Kỳ học cần tìm kiếm. Example: semester_1
     *
     * @response 200 {
     *     "status": "success",
     *     "message": "Tìm kiếm thành công.",
     *     "data": [Chi tiết đánh giá năng lực tìm được dựa trên các tiêu chí]
     * }
     * @response 200 {
     *     "status": "success",
     *     "message": "Không có dữ liệu phù hợp với tiêu chí tìm kiếm.",
     *     "data": []
     * }
     * @response 500 {
     *     "status": "error",
     *     "message": "Lỗi server nội bộ."
     * }
     *
     * @param ChildEvaluationSearchRequest $request Yêu cầu tìm kiếm bao gồm các tham số lọc.
     * @return JsonResponse Trả về kết quả tìm kiếm đánh giá năng lực của trẻ hoặc lỗi nếu có.
     */
    public function search(ChildEvaluationSearchRequest $request): JsonResponse
    {
        try {
            $response = $this->service->search($request);
            if ($response) {
                return $this->jsonResponseSuccess(new ChildEvaluationDetailResource($response));

            }
            return $this->jsonResponseSuccess([]);
        } catch (Exception $exception) {
            $this->logError('Search  failed:', $exception);
            return $this->jsonResponseError('Search', 500);
        }
    }

}
