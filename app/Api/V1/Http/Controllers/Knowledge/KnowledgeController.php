<?php

namespace App\Api\V1\Http\Controllers\Knowledge;

use App\Api\V1\Http\Resources\Knowledge\KnowledgeCategoryResource;
use App\Api\V1\Http\Resources\Knowledge\KnowledgePostDetailResource;
use App\Api\V1\Http\Resources\Knowledge\KnowledgePostResource;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Enums\FeaturedStatus;
use App\Enums\Post\PostStatus;
use App\Enums\PostCategory\PostCategoryStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Traits\MessageSystem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Group Kiến Thức Chăm Con (Parenting Knowledge)
 */
class KnowledgeController extends Controller
{
    use Response, UseLog;

    /**
     * Dữ liệu tổng quan Kiến Thức Chăm Con
     *
     * Lấy danh mục, bài viết nổi bật và bài viết mới nhất
     *
     * @return JsonResponse
     */
    public function overview(): JsonResponse
    {
        try {
            $categories = PostCategory::query()
                ->where('status', PostCategoryStatus::Published)
                ->withCount(['posts' => function ($q) {
                    $q->where('status', PostStatus::Published);
                }])
                ->orderBy('position', 'asc')
                ->get();

            $featuredPosts = Post::query()
                ->where('status', PostStatus::Published)
                ->where('is_featured', FeaturedStatus::Featured)
                ->with('categories')
                ->orderByDesc('id')
                ->take(5)
                ->get();

            $latestPosts = Post::query()
                ->where('status', PostStatus::Published)
                ->with('categories')
                ->orderByDesc('id')
                ->take(10)
                ->get();

            $data = [
                'categories' => KnowledgeCategoryResource::collection($categories),
                'featured_posts' => KnowledgePostResource::collection($featuredPosts),
                'latest_posts' => KnowledgePostResource::collection($latestPosts),
            ];

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $this->logError('Get knowledge overview failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Danh sách chuyên mục Kiến Thức Chăm Con
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = PostCategory::query()
                ->where('status', PostCategoryStatus::Published)
                ->withCount(['posts' => function ($q) {
                    $q->where('status', PostStatus::Published);
                }])
                ->orderBy('position', 'asc')
                ->get();

            return $this->jsonResponseSuccess(KnowledgeCategoryResource::collection($categories));
        } catch (Exception $e) {
            $this->logError('Get knowledge categories failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Danh sách bài viết Kiến Thức Chăm Con
     *
     * Hỗ trợ tìm kiếm, lọc theo danh mục, phân trang
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function posts(Request $request): JsonResponse
    {
        try {
            $page = (int) $request->input('page', 1);
            $limit = (int) $request->input('limit', 10);
            $categoryId = $request->input('category_id');
            $search = $request->input('search');
            $isFeatured = $request->input('is_featured');

            $query = Post::query()
                ->where('status', PostStatus::Published)
                ->with('categories');

            if ($categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('posts_categories.id', $categoryId);
                });
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%");
                });
            }

            if ($isFeatured !== null && $isFeatured !== '') {
                $query->where('is_featured', (int) $isFeatured);
            }

            $posts = $query
                ->orderByDesc('is_featured')
                ->orderByDesc('id')
                ->paginate($limit, ['*'], 'page', $page);

            return $this->jsonResponseSuccess(KnowledgePostResource::collection($posts));
        } catch (Exception $e) {
            $this->logError('Get knowledge posts failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Chi tiết bài viết Kiến Thức Chăm Con
     *
     * Kèm bài viết liên quan cùng chuyên mục
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $post = Post::query()
                ->where('status', PostStatus::Published)
                ->with('categories')
                ->find($id);

            if (!$post) {
                return $this->jsonResponseError('Không tìm thấy bài viết.', 404);
            }

            // Lấy các bài viết liên quan cùng chuyên mục
            $categoryIds = $post->categories->pluck('id')->toArray();
            $relatedPosts = [];

            if (!empty($categoryIds)) {
                $relatedPosts = Post::query()
                    ->where('status', PostStatus::Published)
                    ->where('id', '!=', $post->id)
                    ->whereHas('categories', function ($q) use ($categoryIds) {
                        $q->whereIn('posts_categories.id', $categoryIds);
                    })
                    ->with('categories')
                    ->orderByDesc('id')
                    ->take(4)
                    ->get();
            }

            $data = [
                'post' => new KnowledgePostDetailResource($post),
                'related_posts' => KnowledgePostResource::collection($relatedPosts),
            ];

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $this->logError('Get knowledge post detail failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }
}
