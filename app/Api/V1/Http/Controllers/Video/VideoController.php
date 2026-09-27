<?php

namespace App\Api\V1\Http\Controllers\Video;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Video\AgeGroupRequest;
use App\Api\V1\Http\Requests\Video\VideoCategoryRequest;
use App\Api\V1\Http\Requests\Video\VideoListRequest;
use App\Api\V1\Http\Resources\Video\AgeGroupResource;
use App\Api\V1\Http\Resources\Video\VideoCategoryResource;
use App\Api\V1\Http\Resources\Video\VideoResource;
use App\Api\V1\Repositories\Video\VideoRepositoryInterface;
use App\Api\V1\Services\Video\VideoServiceInterface;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @group Video Giáo dục
 */
class VideoController extends Controller
{
    use Response, UseLog;

    public function __construct(
        VideoRepositoryInterface $repository,
        VideoServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Danh sách Nhóm độ tuổi
     *
     * Hỗ trợ truyền ?child_id=X hoặc ?child_age_months=X để tự động lấy 3 nhóm tuổi gần nhất với tuổi của trẻ
     *
     * @queryParam child_id integer ID của trẻ (bắt buộc đăng nhập để xác thực quyền sở hữu)
     * @queryParam child_age_months integer Tuổi của trẻ theo số tháng (0 - 240)
     *
     * @return JsonResponse
     */
    public function getAgeGroups(AgeGroupRequest $request): JsonResponse
    {
        try {
            $groups = $this->service->getAgeGroups($request);
            return $this->jsonResponseSuccess(AgeGroupResource::collection($groups));
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Get AgeGroups failed:', $e);
            return $this->jsonResponseError('Lấy danh sách nhóm tuổi thất bại', 500);
        }
    }

    /**
     * Danh mục Video theo nhóm tuổi
     *
     * @queryParam age_group_id integer ID của nhóm tuổi
     *
     * @return JsonResponse
     */
    public function getCategories(VideoCategoryRequest $request): JsonResponse
    {
        try {
            $categories = $this->service->getCategories($request);
            return $this->jsonResponseSuccess(VideoCategoryResource::collection($categories));
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Get Video Categories failed:', $e);
            return $this->jsonResponseError('Lấy danh mục video thất bại', 500);
        }
    }

    /**
     * Danh sách Video (kèm phân quyền Free/VIP)
     *
     * @queryParam category_id integer ID danh mục video
     * @queryParam video_category_id integer ID danh mục video (alias)
     * @queryParam age_group_id integer ID nhóm tuổi
     * @queryParam access_type string 'free' hoặc 'vip'
     * @queryParam keyword string Tìm kiếm theo tiêu đề (tối đa 100 ký tự)
     * @queryParam page integer Trang hiện tại (mặc định 1)
     * @queryParam limit integer Số lượng trên trang (1 - 50, mặc định 15)
     *
     * @return JsonResponse
     */
    public function getList(VideoListRequest $request): JsonResponse
    {
        try {
            $result = $this->service->getVideoList($request);
            return $this->jsonResponseSuccess([
                'videos' => VideoResource::collection($result['videos']),
                'pagination' => $result['pagination'],
            ]);
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Get Videos failed:', $e);
            return $this->jsonResponseError('Lấy danh sách video thất bại', 500);
        }
    }

    /**
     * Chi tiết Video (kèm phân quyền Free/VIP và danh sách video liên quan)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $result = $this->service->getVideoDetail((int) $id);
            return $this->jsonResponseSuccess([
                'video' => new VideoResource($result['video']),
                'related_videos' => VideoResource::collection($result['related_videos']),
            ]);
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Get Video detail failed:', $e);
            return $this->jsonResponseError('Không tìm thấy video hoặc video đã bị ẩn', 404);
        }
    }

    /**
     * Tăng lượt xem video (kèm rate limit, deduplication và kiểm tra quyền VIP)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function incrementView(Request $request, $id): JsonResponse
    {
        try {
            $result = $this->service->incrementView($request, (int) $id);
            return $this->jsonResponseSuccess($result, __('Cập nhật lượt xem thành công.'));
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Increment video view failed:', $e);
            return $this->jsonResponseError('Không tìm thấy video hoặc video đã bị ẩn', 404);
        }
    }
}
