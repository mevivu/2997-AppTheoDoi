<?php

namespace App\Api\V1\Http\Controllers\Lesson;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Lesson\LessonAgeGroupRequest;
use App\Api\V1\Http\Requests\Lesson\LessonCategoryRequest;
use App\Api\V1\Http\Requests\Lesson\LessonDifficultyRatingRequest;
use App\Api\V1\Http\Requests\Lesson\LessonListRequest;
use App\Api\V1\Http\Requests\Lesson\LessonPillarRequest;
use App\Api\V1\Http\Resources\Lesson\AgeGroupResource;
use App\Api\V1\Http\Resources\Lesson\LessonCategoryResource;
use App\Api\V1\Http\Resources\Lesson\LessonDetailResource;
use App\Api\V1\Http\Resources\Lesson\LessonResource;
use App\Api\V1\Http\Resources\Lesson\PillarResource;
use App\Api\V1\Repositories\Lesson\LessonRepositoryInterface;
use App\Api\V1\Services\Lesson\LessonServiceInterface;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @group Bài học giáo dục (5 Trụ cột)
 */
class LessonController extends Controller
{
    use Response, UseLog;

    public function __construct(
        LessonRepositoryInterface $repository,
        LessonServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Tầng 1: Danh sách Nhóm độ tuổi (Thanh lượn sóng)
     *
     * Hỗ trợ truyền ?child_id=X hoặc ?child_age_months=X để tự động đánh dấu nhóm tuổi hiện tại của trẻ (is_current: true)
     *
     * @queryParam child_id integer ID của trẻ (bắt buộc đăng nhập để xác thực)
     * @queryParam child_age_months integer Tuổi của trẻ theo số tháng (0 - 240)
     *
     * @return JsonResponse
     */
    public function getAgeGroups(LessonAgeGroupRequest $request): JsonResponse
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
     * Tầng 2: Danh sách 5 Trụ cột giáo dục (Vòng cung bán nguyệt)
     *
     * @queryParam age_group_id integer ID nhóm tuổi (để thống kê số bài học tương ứng theo độ tuổi)
     *
     * @return JsonResponse
     */
    public function getPillars(LessonPillarRequest $request): JsonResponse
    {
        try {
            $pillars = $this->service->getPillars($request);
            return $this->jsonResponseSuccess(PillarResource::collection($pillars));
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Get Pillars failed:', $e);
            return $this->jsonResponseError('Lấy thông tin trụ cột giáo dục thất bại', 500);
        }
    }

    /**
     * Tầng 3: Lưới danh mục / kỹ năng theo Nhóm tuổi & Trụ cột
     *
     * @queryParam age_group_id integer ID nhóm tuổi (bắt buộc)
     * @queryParam pillar string Trụ cột giáo dục: pq, iq, eq, aq, thai_giao
     *
     * @return JsonResponse
     */
    public function getCategories(LessonCategoryRequest $request): JsonResponse
    {
        try {
            $categories = $this->service->getCategories($request);
            return $this->jsonResponseSuccess(LessonCategoryResource::collection($categories));
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Get Lesson Categories failed:', $e);
            return $this->jsonResponseError('Lấy danh mục bài học thất bại', 500);
        }
    }

    /**
     * Tầng 4: Danh sách bài học phân trang (kèm phân quyền Free/VIP)
     *
     * @queryParam category_id integer ID danh mục bài học
     * @queryParam lesson_category_id integer ID danh mục bài học (alias)
     * @queryParam age_group_id integer ID nhóm tuổi
     * @queryParam pillar string Trụ cột giáo dục (pq, iq, eq, aq, thai_giao)
     * @queryParam access_type string free, vip hoặc all
     * @queryParam difficulty string easy, medium hoặc hard
     * @queryParam keyword string Tìm kiếm theo tiêu đề hoặc nội dung
     * @queryParam page integer Trang hiện tại (mặc định 1)
     * @queryParam limit integer Số lượng trên trang (1 - 50, mặc định 15)
     *
     * @return JsonResponse
     */
    public function getList(LessonListRequest $request): JsonResponse
    {
        try {
            $result = $this->service->getLessonList($request);
            $lessonCollection = LessonResource::collection($result['lessons']);
            return $this->jsonResponseSuccess([
                'lessons' => $lessonCollection,
                'videos' => $lessonCollection, // Tương thích ngược cho App phiên bản cũ
                'pagination' => $result['pagination'],
            ]);
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Get Lessons failed:', $e);
            return $this->jsonResponseError('Lấy danh sách bài học thất bại', 500);
        }
    }

    /**
     * Chi tiết bài học & Video Player
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $result = $this->service->getLessonDetail((int) $id);
            $lessonDetailResource = new LessonDetailResource($result['lesson']);
            $relatedLessonsResource = LessonResource::collection($result['related_lessons']);

            return $this->jsonResponseSuccess([
                'lesson' => $lessonDetailResource,
                'video' => $lessonDetailResource, // Tương thích ngược cho App phiên bản cũ
                'related_lessons' => $relatedLessonsResource,
                'related_videos' => $relatedLessonsResource, // Tương thích ngược cho App phiên bản cũ
            ]);
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Get Lesson detail failed:', $e);
            return $this->jsonResponseError('Không tìm thấy bài học hoặc bài học đã bị ẩn', 404);
        }
    }

    /**
     * Tăng lượt xem bài học (kèm rate limit, deduplication và phân quyền VIP)
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
            $this->logError('Increment lesson view failed:', $e);
            return $this->jsonResponseError('Không tìm thấy bài học hoặc bài học đã bị ẩn', 404);
        }
    }

    /**
     * Toggle đánh giá độ khó bài học (tạo / cập nhật / xóa)
     *
     * @bodyParam child_id integer required ID hồ sơ bé
     * @bodyParam difficulty_level string required Mức đánh giá: easy, with_help, hard
     *
     * @param LessonDifficultyRatingRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function rateDifficulty(LessonDifficultyRatingRequest $request, $id): JsonResponse
    {
        try {
            $result = $this->service->toggleDifficultyRating(
                (int) $id,
                $request->filled('child_id') ? (int) $request->input('child_id') : null,
                $request->input('difficulty_level')
            );
            return $this->jsonResponseSuccess($result, __('Đánh giá đã được cập nhật.'));
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Rate lesson difficulty failed:', $e);
            return $this->jsonResponseError('Đánh giá bài học thất bại, vui lòng thử lại.', 500);
        }
    }

    /**
     * Thống kê đánh giá độ khó bài học (phần trăm mỗi mức)
     *
     * @queryParam child_id integer ID hồ sơ bé (để lấy đánh giá hiện tại của user cho bé)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getDifficultyStats(Request $request, $id): JsonResponse
    {
        try {
            $childId = $request->filled('child_id') ? (int) $request->input('child_id') : null;
            $result = $this->service->getDifficultyStats((int) $id, $childId);
            return $this->jsonResponseSuccess($result);
        } catch (HttpException $e) {
            return $this->jsonResponseError($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            $this->logError('Get difficulty stats failed:', $e);
            return $this->jsonResponseError('Lấy thống kê đánh giá thất bại.', 500);
        }
    }
}
