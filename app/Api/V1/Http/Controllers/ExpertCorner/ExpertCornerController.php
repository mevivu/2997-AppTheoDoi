<?php

namespace App\Api\V1\Http\Controllers\ExpertCorner;

use App\Api\V1\Http\Resources\Expert\ExpertResource;
use App\Api\V1\Http\Resources\ExpertCategory\ExpertCategoryResource;
use App\Api\V1\Http\Resources\ExpertPost\ExpertPostDetailResource;
use App\Api\V1\Http\Resources\ExpertPost\ExpertPostResource;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Enums\DefaultStatus;
use App\Http\Controllers\Controller;
use App\Models\Expert;
use App\Models\ExpertCategory;
use App\Models\ExpertPost;
use App\Traits\MessageSystem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Group Góc Chuyên Gia & Kiến thức Y khoa
 */
class ExpertCornerController extends Controller
{
    use Response, UseLog;

    /**
     * Dữ liệu tổng quan Góc Chuyên Gia
     *
     * Lấy chuyên mục, chuyên gia nổi bật, bài viết ghim và bài viết mới nhất
     *
     * @return JsonResponse
     */
    public function overview(): JsonResponse
    {
        try {
            $strategicExperts = Expert::query()
                ->where('status', DefaultStatus::Published)
                ->where('council_type', \App\Enums\Expert\ExpertCouncilType::Strategic)
                ->orderBy('sort_order', 'asc')
                ->get();

            $professionalExperts = Expert::query()
                ->where('status', DefaultStatus::Published)
                ->where('council_type', \App\Enums\Expert\ExpertCouncilType::Professional)
                ->orderBy('sort_order', 'asc')
                ->get();

            $categories = ExpertCategory::query()
                ->where('status', DefaultStatus::Published)
                ->orderBy('sort_order', 'asc')
                ->get();

            $experts = Expert::query()
                ->where('status', DefaultStatus::Published)
                ->orderBy('sort_order', 'asc')
                ->take(10)
                ->get();

            $featuredPosts = ExpertPost::query()
                ->where('status', DefaultStatus::Published)
                ->where('is_featured', 1)
                ->with(['expert', 'category', 'ageGroup'])
                ->orderByDesc('id')
                ->take(5)
                ->get();

            $latestPosts = ExpertPost::query()
                ->where('status', DefaultStatus::Published)
                ->with(['expert', 'category', 'ageGroup'])
                ->orderByDesc('id')
                ->take(10)
                ->get();

            $data = [
                'strategic_councils' => [
                    'title' => 'HỘI ĐỒNG CỐ VẤN CHIẾN LƯỢC CHĂM CON 360',
                    'council_type' => 1,
                    'members' => ExpertResource::collection($strategicExperts),
                ],
                'professional_councils' => [
                    'title' => 'HỘI ĐỒNG TƯ VẤN CHUYÊN MÔN CHĂM CON 360',
                    'council_type' => 2,
                    'members' => ExpertResource::collection($professionalExperts),
                ],
                'categories' => ExpertCategoryResource::collection($categories),
                'featured_experts' => ExpertResource::collection($experts),
                'featured_posts' => ExpertPostResource::collection($featuredPosts),
                'latest_posts' => ExpertPostResource::collection($latestPosts),
            ];

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $this->logError('Get expert corner overview failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Danh sách bài viết chuyên gia (kèm bộ lọc & phân trang)
     *
     * @queryParam category_id int Lọc theo chuyên mục. Example: 1
     * @queryParam age_group_id int Lọc theo nhóm độ tuổi. Example: 2
     * @queryParam expert_id int Lọc theo chuyên gia biên soạn. Example: 3
     * @queryParam is_featured int Lọc bài viết nổi bật (1 hoặc 0). Example: 1
     * @queryParam keyword string Từ khóa tìm kiếm. Example: vitamin
     * @queryParam page int Trang hiện tại. Example: 1
     * @queryParam limit int Số bản ghi trên mỗi trang. Example: 10
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function posts(Request $request): JsonResponse
    {
        try {
            $query = ExpertPost::query()
                ->where('status', DefaultStatus::Published)
                ->with(['expert', 'category', 'ageGroup']);

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            if ($request->filled('age_group_id')) {
                $query->where('age_group_id', $request->input('age_group_id'));
            }

            if ($request->filled('expert_id')) {
                $query->where('expert_id', $request->input('expert_id'));
            }

            if ($request->filled('is_featured')) {
                $query->where('is_featured', (int) $request->input('is_featured'));
            }

            if ($request->filled('keyword')) {
                $keyword = trim($request->input('keyword'));
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('excerpt', 'like', "%{$keyword}%")
                        ->orWhere('expert_quote', 'like', "%{$keyword}%")
                        ->orWhereHas('expert', function ($eq) use ($keyword) {
                            $eq->where('name', 'like', "%{$keyword}%");
                        });
                });
            }

            $limit = (int) $request->input('limit', 10);
            $page = (int) $request->input('page', 1);

            $posts = $query->orderByDesc('is_featured')
                ->orderBy('sort_order', 'asc')
                ->orderByDesc('id')
                ->paginate($limit, ['*'], 'page', $page);

            return $this->jsonResponseSuccess(ExpertPostResource::collection($posts));
        } catch (Exception $e) {
            $this->logError('Get expert posts failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Chi tiết bài viết chuyên gia
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $post = ExpertPost::query()
                ->where('status', DefaultStatus::Published)
                ->with(['expert', 'category', 'ageGroup'])
                ->where(function ($q) use ($id) {
                    if (is_numeric($id)) {
                        $q->where('id', $id);
                    } else {
                        $q->where('slug', $id);
                    }
                })
                ->first();

            if (!$post) {
                return $this->jsonResponseError('Không tìm thấy bài viết chuyên gia', 404);
            }

            // Tăng lượt xem an toàn
            $post->increment('views');

            // Lấy 4 bài viết liên quan cùng chuyên mục
            $relatedPosts = ExpertPost::query()
                ->where('status', DefaultStatus::Published)
                ->where('id', '!=', $post->id)
                ->when($post->category_id, function ($q) use ($post) {
                    $q->where('category_id', $post->category_id);
                })
                ->with(['expert', 'category', 'ageGroup'])
                ->orderByDesc('id')
                ->take(4)
                ->get();

            $post->related_posts = $relatedPosts;

            return $this->jsonResponseSuccess(new ExpertPostDetailResource($post));
        } catch (Exception $e) {
            $this->logError('Get expert post detail failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Danh sách chuyên gia / Bác sĩ cố vấn (có thể lọc theo council_type)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function experts(Request $request): JsonResponse
    {
        try {
            $query = Expert::query()
                ->where('status', DefaultStatus::Published);

            if ($request->filled('council_type')) {
                $query->where('council_type', (int) $request->input('council_type'));
            }

            $experts = $query->orderBy('sort_order', 'asc')->get();

            return $this->jsonResponseSuccess(ExpertResource::collection($experts));
        } catch (Exception $e) {
            $this->logError('Get experts failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Chi tiết chuyên gia kèm danh sách bài viết
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function expertDetail($id): JsonResponse
    {
        try {
            $expert = Expert::query()
                ->where('status', DefaultStatus::Published)
                ->find($id);

            if (!$expert) {
                return $this->jsonResponseError('Không tìm thấy thông tin chuyên gia', 404);
            }

            $posts = ExpertPost::query()
                ->where('status', DefaultStatus::Published)
                ->where('expert_id', $expert->id)
                ->with(['category', 'ageGroup'])
                ->orderByDesc('id')
                ->take(10)
                ->get();

            $data = [
                'expert' => new ExpertResource($expert),
                'posts' => ExpertPostResource::collection($posts),
            ];

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $this->logError('Get expert detail failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Danh sách chuyên mục chuyên đề
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = ExpertCategory::query()
                ->where('status', DefaultStatus::Published)
                ->orderBy('sort_order', 'asc')
                ->get();

            return $this->jsonResponseSuccess(ExpertCategoryResource::collection($categories));
        } catch (Exception $e) {
            $this->logError('Get expert categories failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }
}
