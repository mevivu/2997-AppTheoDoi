<?php

namespace App\Api\V1\Http\Controllers\Child;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\Child\ChildRequest;
use App\Api\V1\Http\Requests\Child\ChildUpdateRequest;
use App\Api\V1\Http\Resources\Child\ChildResource;
use App\Api\V1\Http\Resources\Child\ChildResourceCollection;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Services\Child\ChildServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Validate\Validator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Con
 */
class ChildController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        ChildRepositoryInterface $repository,
        ChildServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');

    }

    /**
     * Lưu thông tin đứa trẻ
     *
     * API này cho phép người dùng lưu thông tin về một đứa trẻ mới, bao gồm các chi tiết như tên đầy đủ, giới tính, ngày sinh, tình trạng đã sinh hay chưa sinh và avatar.
     *
     * @bodyParam fullname string required Tên đầy đủ của đứa trẻ. Example: John Doe
     * @bodyParam gender int required Giới tính của đứa trẻ. Example: 1 (Nam)
     * @bodyParam is_born string required Tình trạng đã sinh hay chưa sinh của đứa trẻ. Example: born
     * @bodyParam birthday string nullable Ngày sinh của đứa trẻ. Example: 2024-12-12
     * @bodyParam avatar string nullable Đường dẫn hình ảnh đại diện của đứa trẻ. Example: http://example.com/avatar.jpg
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thông tin đứa trẻ đã được lưu thành công.",
     *     "data": {
     *         "id": 1,
     *         "fullname": "John Doe",
     *         "gender": 1,
     *         "is_born": "born",
     *         "birthday": "2024-12-12",
     *         "avatar": "http://example.com/avatar.jpg"
     *     }
     * }
     *
     * @response 422 {
     *     "status": 422,
     *     "message": "Dữ liệu không hợp lệ.",
     *     "errors": {
     *         "fullname": ["Tên đầy đủ không được để trống."],
     *         "gender": ["Giới tính không hợp lệ."],
     *         "is_born": ["Tình trạng đã sinh hoặc chưa sinh là bắt buộc."],
     *         "birthday": ["Ngày sinh phải theo định dạng Y-m-d."]
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lưu thông tin đứa trẻ."
     * }
     *
     * @param ChildRequest $request
     * @return JsonResponse
     */
    public function store(ChildRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->store($request);
            DB::commit();
            return $this->jsonResponseSuccess(new ChildResource($response));
        } catch (Exception $exception) {
            DB::rollBack();
            $this->logError('Child Store failed:', $exception);
            return $this->jsonResponseError('Get user notifications failed', 500);
        }

    }

    /**
     * Cập nhật thông tin đứa trẻ
     *
     * API này cho phép người dùng cập nhật thông tin của một đứa trẻ, bao gồm các chi tiết như tên đầy đủ, giới tính, ngày sinh, tình trạng đã sinh hay chưa sinh và avatar.
     *
     * @authenticated
     * @bodyParam fullname string required Tên đầy đủ mới của đứa trẻ. Example: Jane Doe
     * @bodyParam gender int required Giới tính mới của đứa trẻ. Example: 2 (Nữ)
     * @bodyParam is_born string required Tình trạng đã sinh hay chưa sinh của đứa trẻ. Example: unborn
     * @bodyParam birthday string nullable Ngày sinh mới của đứa trẻ. Example: 2025-01-01
     * @bodyParam avatar string nullable Đường dẫn hình ảnh đại diện mới của đứa trẻ. Example: http://example.com/new-avatar.jpg
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thông tin đứa trẻ đã được cập nhật thành công.",
     *     "data": {
     *         "id": 1,
     *         "fullname": "Jane Doe",
     *         "gender": 2,
     *         "is_born": "unborn",
     *         "birthday": "2025-01-01",
     *         "avatar": "http://example.com/new-avatar.jpg"
     *     }
     * }
     *
     * @response 422 {
     *     "status": 422,
     *     "message": "Dữ liệu không hợp lệ.",
     *     "errors": {
     *         "fullname": ["Tên đầy đủ không được để trống."],
     *         "gender": ["Giới tính không hợp lệ."],
     *         "is_born": ["Tình trạng đã sinh hoặc chưa sinh là bắt buộc."],
     *         "birthday": ["Ngày sinh phải theo định dạng Y-m-d."]
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi cập nhật thông tin đứa trẻ."
     * }
     *
     * @param ChildUpdateRequest $request
     * @return JsonResponse
     */
    public function update(ChildUpdateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->update($request);
            DB::commit();
            return $this->jsonResponseSuccess(new ChildResource($response));
        } catch (Exception $exception) {
            DB::rollBack();
            $this->logError('Child Update failed:', $exception);
            return $this->jsonResponseError('Failed to update child information', 500);
        }
    }

    /**
     * Xoá thông tin đứa trẻ
     *
     * API này cho phép người dùng xoá thông tin của một đứa trẻ. Khi một đứa trẻ bị xoá, tất cả các dữ liệu liên quan đến đứa trẻ đó sẽ bị loại bỏ khỏi hệ thống.
     *
     * @authenticated
     * @pathParam  id int required ID của đứa trẻ cần xoá. Example: 1
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thông tin đứa trẻ đã được xoá thành công."
     * }
     *
     * @response 422 {
     *     "status": 422,
     *     "message": "Dữ liệu không hợp lệ.",
     *     "errors": {
     *         "id": ["ID của đứa trẻ không hợp lệ."]
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi xoá thông tin đứa trẻ."
     * }
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            Validator::validateExists($this->repository, $id);
            $this->repository->delete($id);
            return $this->jsonResponseSuccess('Delete child successfully');
        } catch (BadRequestException|NotFoundException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $e) {
            $this->logError('Delete child failed:', $e);
            return $this->jsonResponseError('Delete child failed', 500);
        }
    }

    /**
     * Lấy danh sách các đứa trẻ theo người dùnngff
     *
     * API này cho phép người dùng lấy danh sách các đứa trẻ của họ, bao gồm các thông tin như tên, giới tính, tình trạng đã sinh hay chưa sinh, ngày sinh, và avatar.
     * Người dùng có thể lọc và phân trang kết quả.
     *
     * @authenticated
     * @queryParam page int Trang hiện tại của kết quả phân trang. Default: 1. Example: 2
     * @queryParam limit int Số lượng kết quả mỗi trang. Default: 10. Example: 20
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Lấy danh sách đứa trẻ thành công.",
     *     "data": {
     *         "current_page": 1,
     *         "data": [
     *             {
     *                 "id": 1,
     *                 "fullname": "John Doe",
     *                 "gender": 1,
     *                 "is_born": "born",
     *                 "birthday": "2024-12-12",
     *                 "avatar": "http://example.com/avatar.jpg"
     *             },
     *             {
     *                 "id": 2,
     *                 "fullname": "Jane Smith",
     *                 "gender": 2,
     *                 "is_born": "unborn",
     *                 "birthday": "2025-01-01",
     *                 "avatar": "http://example.com/avatar2.jpg"
     *             }
     *         ],
     *         "first_page_url": "http://example.com/api/children?page=1",
     *         "from": 1,
     *         "last_page": 1,
     *         "last_page_url": "http://example.com/api/children?page=1",
     *         "next_page_url": null,
     *         "path": "http://example.com/api/children",
     *         "per_page": 10,
     *         "prev_page_url": null,
     *         "to": 1,
     *         "total": 1
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách đứa trẻ."
     * }
     *
     * @param ChildRequest $request
     * @return JsonResponse
     */
    public function index(ChildRequest $request): JsonResponse
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess(new ChildResourceCollection($response));
        } catch (Exception $exception) {
            $this->logError('Get Children List failed:', $exception);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh sách đứa trẻ.', 500);
        }
    }

    /**
     * Lấy thông tin chi tiết của một đứa trẻ
     *
     * API này cho phép người dùng lấy thông tin chi tiết của một đứa trẻ dựa trên ID của đứa trẻ.
     * Kết quả trả về sẽ bao gồm các chi tiết như tên, giới tính, tình trạng đã sinh hay chưa sinh, ngày sinh, và avatar.
     *
     * @authenticated
     * @pathParam id int required ID của đứa trẻ cần lấy thông tin. Example: 1
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Lấy thông tin đứa trẻ thành công.",
     *     "data": {
     *         "id": 1,
     *         "fullname": "John Doe",
     *         "gender": 1,
     *         "is_born": "born",
     *         "birthday": "2024-12-12",
     *         "avatar": "http://example.com/avatar.jpg"
     *     }
     * }
     *
     * @response 422 {
     *     "status": 422,
     *     "message": "Dữ liệu không hợp lệ.",
     *     "errors": {
     *         "id": ["ID của đứa trẻ không hợp lệ."]
     *     }
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Đứa trẻ không tìm thấy."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy thông tin đứa trẻ."
     * }
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            Validator::validateExists($this->repository, $id);
            $response = $this->repository->findOrFail($id);
            return $this->jsonResponseSuccess(new ChildResource($response));
        } catch (BadRequestException|NotFoundException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $e) {
            $this->logError('Show detail child failed:', $e);
            return $this->jsonResponseError('Show detail child failed', 500);
        }
    }


}
