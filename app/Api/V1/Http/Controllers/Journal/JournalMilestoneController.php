<?php

namespace App\Api\V1\Http\Controllers\Journal;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\Journal\JournalMilestoneListRequest;
use App\Api\V1\Http\Requests\Journal\JournalMilestoneStoreRequest;
use App\Api\V1\Http\Requests\Journal\JournalMilestoneUpdateRequest;
use App\Api\V1\Services\Journal\JournalMilestoneServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @group Danh mục cột mốc nhật ký
 */
class JournalMilestoneController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(JournalMilestoneServiceInterface $service)
    {
        $this->service = $service;
        $this->middleware('auth:api');
    }

    /**
     * Lấy danh mục cột mốc nhật ký theo giai đoạn cho bé
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     */
    public function index(JournalMilestoneListRequest $request): JsonResponse
    {
        try {
            $data = $this->service->getMilestonesForChild($request);
            return $this->jsonResponseSuccess($data, 'Lấy danh mục cột mốc thành công.');
        } catch (NotFoundException $e) {
            return $this->jsonResponseError($e->getMessage(), 404);
        } catch (BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->logError('Get journal milestones failed:', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh mục cột mốc.', 500);
        }
    }

    /**
     * Phụ huynh tạo cột mốc riêng cho bé
     *
     * @authenticated
     */
    public function store(JournalMilestoneStoreRequest $request): JsonResponse
    {
        try {
            $milestone = $this->service->storeUserMilestone($request);
            return $this->jsonResponseSuccess($milestone, 'Tạo cột mốc thành công.');
        } catch (NotFoundException $e) {
            return $this->jsonResponseError($e->getMessage(), 404);
        } catch (BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->logError('Store user milestone failed:', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi tạo cột mốc.', 500);
        }
    }

    /**
     * Phụ huynh cập nhật cột mốc riêng của bé
     *
     * @authenticated
     */
    public function update(int $id, JournalMilestoneUpdateRequest $request): JsonResponse
    {
        try {
            $milestone = $this->service->updateUserMilestone($id, $request);
            return $this->jsonResponseSuccess($milestone, 'Cập nhật cột mốc thành công.');
        } catch (NotFoundException $e) {
            return $this->jsonResponseError($e->getMessage(), 404);
        } catch (BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->logError('Update user milestone failed:', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi cập nhật cột mốc.', 500);
        }
    }

    /**
     * Phụ huynh xoá cột mốc riêng của bé
     *
     * @authenticated
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->deleteUserMilestone($id);
            return $this->jsonResponseSuccess(null, 'Xoá cột mốc thành công.');
        } catch (NotFoundException $e) {
            return $this->jsonResponseError($e->getMessage(), 404);
        } catch (BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->logError('Delete user milestone failed:', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi xoá cột mốc.', 500);
        }
    }
}
