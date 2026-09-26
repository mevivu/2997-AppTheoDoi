<?php

namespace App\Api\V1\Http\Controllers\Exercise;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\Exercise\ExerciseRequest;


use App\Api\V1\Http\Resources\Exercise\ExerciseCollection;
use App\Api\V1\Http\Resources\Exercise\ExerciseDetailCollection;

use App\Api\V1\Repositories\Exercise\ExerciseRepositoryInterface;
use App\Api\V1\Services\Exercise\ExerciseServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Validate\Validator;
use Exception;
use Illuminate\Http\JsonResponse;


/**
 * @group Bài tập
 */
class ExerciseController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        ExerciseRepositoryInterface $repository,
        ExerciseServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * DS Exercise
     *
     * DS bài tập
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     * Kiểu bài tập (exercise_type) gồm:
     * - physical: thuộc thể chất
     * - power: thuộc sức mạnh
     * @queryParam page integer
     * Trang hiện tại, page > 0. Example: 1
     *
     * @queryParam limit integer
     * Số lượng thông báo trong 1 trang, limit > 0. Example: 1
     *
     *
     * @response 200 {
     *    "status": 200,
     *    "message": "Thực hiện thành công.",
     *    "data": [
     *        {
     *               "id": 1,
     *               "name": "Bài tập thể chất 1",
     *                "status": "active"
     *        },
     *        {
     *            "id": 2,
     *            "name": "Bài tập sức mạnh 1",
     *             "status": "active"
     *        }
     *    ]
     * }
     *
     * @param ExerciseRequest $request
     *
     * @return JsonResponse
     */
    public function index(ExerciseRequest $request): JsonResponse
    {
        try {
            return $this->jsonResponseSuccess(new ExerciseCollection($this->service->index($request)));
        } catch (Exception $exception) {
            $this->logError('Get Exercises failed:', $exception);
            return $this->jsonResponseError('Get Exercises failed', 500);
        }

    }

    /**
     * Chi tiết bài tập
     *
     * lấy chi tiết  bài tập
     * @pathParam id integer required
     * ID
     * @response 200 {
     *    "status": 200,
     *    "message": "Thực hiện thành công.",
     *    "data": [
     *        {
     *             "id": 1,
     *               "name": "Bài tập thể chất 1",
     *               "description": "<p>B&agrave;i tập thể chất 1</p>",
     *               "status": "active",
     *               "exercise_type": "physical"
     *        }
     *
     *    ]
     * }
     *
     * @param $id
     * @return JsonResponse
     */
    public function detail($id): JsonResponse
    {
        try {
            Validator::validateExists($this->repository, $id);
            return $this->jsonResponseSuccess(new ExerciseDetailCollection($this->repository->findOrFail($id)));
        } catch (NotFoundException|BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $exception) {
            $this->logError('Get detail Exercises failed:', $exception);
            return $this->jsonResponseError('Get detail Exercises failed', 500);
        }
    }

    /**
     * Kiểm tra user hiện tại có gói VIP đang hoạt động hay không
     */
    protected function isVipUser(): bool
    {
        $user = auth('api')->user();
        if (!$user) {
            return false;
        }

        return $user->userPackages()
            ->where('status', \App\Enums\Package\PackageUserStatus::Active)
            ->where('end_date', '>=', now())
            ->where('current_type', '!=', \App\Enums\Package\PackageType::Normal)
            ->exists();
    }

    /**
     * Danh mục Bài tập theo Chủ đề và Nhóm tuổi
     *
     * @queryParam topic string 'pq', 'iq', 'eq', 'aq', 'thai_giao'
     * @queryParam age_group_id integer ID nhóm tuổi
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getCategories(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $query = \App\Models\ExerciseCategory::active()
                ->with('ageGroup')
                ->withCount('exercises');

            if ($request->filled('topic')) {
                $query->where('topic', $request->input('topic'));
            }

            if ($request->filled('age_group_id')) {
                $query->where('age_group_id', $request->input('age_group_id'));
            }

            $categories = $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->get();

            return $this->jsonResponseSuccess(\App\Api\V1\Http\Resources\Exercise\ExerciseCategoryResource::collection($categories));
        } catch (Exception $e) {
            $this->logError('Get Exercise Categories failed:', $e);
            return $this->jsonResponseError('Lấy danh mục bài tập thất bại', 500);
        }
    }

    /**
     * Danh sách Bài tập Giáo dục (PQ, IQ, EQ, AQ, Thai giáo)
     *
     * @queryParam exercise_category_id integer ID danh mục bài tập
     * @queryParam category_id integer ID danh mục bài tập (alias)
     * @queryParam topic string 'pq', 'iq', 'eq', 'aq', 'thai_giao'
     * @queryParam age_group_id integer ID nhóm tuổi
     * @queryParam difficulty string 'easy', 'medium', 'hard', 'assisted'
     * @queryParam keyword string Tìm kiếm theo tên
     * @queryParam page integer
     * @queryParam limit integer
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getEducationList(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $query = \App\Models\Exercise::active()
                ->whereNotNull('exercise_category_id')
                ->with(['category', 'media']);

            $categoryId = $request->input('exercise_category_id', $request->input('category_id'));
            if ($categoryId) {
                $query->where('exercise_category_id', $categoryId);
            }

            if ($request->filled('topic')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('topic', $request->input('topic'));
                });
            }

            if ($request->filled('age_group_id')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('age_group_id', $request->input('age_group_id'));
                });
            }

            if ($request->filled('difficulty')) {
                $query->where('difficulty', $request->input('difficulty'));
            }

            if ($request->filled('keyword')) {
                $query->where('name', 'like', '%' . $request->input('keyword') . '%');
            }

            $limit = max(1, min(50, (int) $request->input('limit', 15)));
            $exercises = $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->paginate($limit);

            $isVip = $this->isVipUser();

            $exercises->getCollection()->transform(function ($item) use ($isVip) {
                if ($isVip) {
                    $item->is_locked = false;
                } else {
                    $item->is_locked = ($item->access_type == \App\Enums\Video\VideoAccessType::VIP);
                }
                return $item;
            });

            return $this->jsonResponseSuccess([
                'exercises' => \App\Api\V1\Http\Resources\Exercise\ExerciseEducationResource::collection($exercises),
                'pagination' => [
                    'current_page' => $exercises->currentPage(),
                    'last_page' => $exercises->lastPage(),
                    'per_page' => $exercises->perPage(),
                    'total' => $exercises->total(),
                ],
            ]);
        } catch (Exception $e) {
            $this->logError('Get Education Exercises failed:', $e);
            return $this->jsonResponseError('Lấy danh sách bài tập thất bại', 500);
        }
    }

    /**
     * Chi tiết Bài tập Giáo dục
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getEducationDetail($id): JsonResponse
    {
        try {
            $exercise = \App\Models\Exercise::active()
                ->whereNotNull('exercise_category_id')
                ->with(['category', 'media' => function ($q) {
                    $q->orderBy('sort_order', 'asc');
                }])
                ->findOrFail($id);

            $isVip = $this->isVipUser();
            $exercise->is_locked = (!$isVip && $exercise->access_type == \App\Enums\Video\VideoAccessType::VIP);

            return $this->jsonResponseSuccess(new \App\Api\V1\Http\Resources\Exercise\ExerciseEducationResource($exercise));
        } catch (Exception $e) {
            $this->logError('Get Education Exercise detail failed:', $e);
            return $this->jsonResponseError('Không tìm thấy bài tập hoặc bài tập đã bị ẩn', 404);
        }
    }

    /**
     * Ghi nhận lượt tập luyện
     *
     * @param int $id
     * @return JsonResponse
     */
    public function incrementPractice($id): JsonResponse
    {
        try {
            $exercise = \App\Models\Exercise::findOrFail($id);
            $exercise->increment('practice_count');

            return $this->jsonResponseSuccess([
                'id' => $exercise->id,
                'practice_count' => (int) $exercise->practice_count,
            ], __('Ghi nhận lượt tập luyện thành công.'));
        } catch (Exception $e) {
            $this->logError('Increment exercise practice failed:', $e);
            return $this->jsonResponseError('Không thể ghi nhận lượt tập luyện', 404);
        }
    }
}
