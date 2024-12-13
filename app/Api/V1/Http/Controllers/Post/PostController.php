<?php

namespace App\Api\V1\Http\Controllers\Post;

use App\Api\V1\Http\Requests\Post\PostRequest;
use App\Api\V1\Http\Resources\Post\PostResource;
use App\Api\V1\Repositories\Post\PostRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Post\PostStatus;
use App\Http\Controllers\Controller;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;

/**
 * Group Bài viết
 */
class PostController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected $repository;

    public function __construct(
        PostRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Danh sách bài viết
     *
     * Lấy danh sách bài viết đã xuất bản
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @queryParam page int Trang hiện tại. Example: 1
     * @queryParam limit int Số lượng bản ghi trên mỗi trang. Example: 10
     *
     * @response {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": [
     *         {
     *             "id": 2,
     *             "title": "bài viết",
     *             "slug": "bai-viet",
     *             "image": "/image.png",
     *             "is_featured": 2,
     *             "excerpt": "Lorem",
     *             "content": "Á há há há",
     *             "posted_at": "13-12-2024 06:40"
     *         }
     *     ]
     * }
     *
     * @param \App\Api\V1\Http\Requests\Post\PostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PostRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $page = $data['page'] ?? 1;
            $limit = $data['limit'] ?? 10;

            $posts = $this->repository->getByQueryBuilder(
                ['status' => PostStatus::Published->value]
            )->paginate($limit, ['*'], 'page', $page);

            return $this->jsonResponseSuccess(PostResource::collection($posts));
        } catch (\Exception $e) {
            $this->logError('Get posts failed:', $e);
            return $this->jsonResponseError('Get posts failed', 500);
        }
    }

    /**
     * Chi tiết bài viết
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @response {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": [
     *         {
     *             "id": 2,
     *             "title": "bài viết",
     *             "slug": "bai-viet",
     *             "image": "/image.png",
     *             "is_featured": 2,
     *             "excerpt": "Lorem",
     *             "content": "Á há há há",
     *             "posted_at": "13-12-2024 06:40"
     *         }
     *     ]
     * }
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $post = $this->repository->find($id);

            if ($post) {
                return $this->jsonResponseSuccess(new PostResource($post));
            }

            return $this->jsonResponseError('Không tìm thấy bài viết', 404);
        } catch (\Exception $e) {
            $this->logError('Get post failed:', $e);
            return $this->jsonResponseError('Get post failed', 500);
        }
    }
}