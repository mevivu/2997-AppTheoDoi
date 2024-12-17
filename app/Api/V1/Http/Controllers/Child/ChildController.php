<?php

namespace App\Api\V1\Http\Controllers\Child;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Child\ChildRequest;
use App\Api\V1\Http\Resources\Child\ChildResource;
use App\Api\V1\Services\Child\ChildServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
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
        ChildServiceInterface $service

    )
    {
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


}
