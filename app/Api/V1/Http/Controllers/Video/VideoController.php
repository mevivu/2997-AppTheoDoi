<?php

namespace App\Api\V1\Http\Controllers\Video;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Video\AgeGroupRequest;
use App\Api\V1\Http\Requests\Video\VideoCategoryRequest;
use App\Api\V1\Http\Requests\Video\VideoListRequest;
use App\Api\V1\Http\Resources\Video\AgeGroupResource;
use App\Api\V1\Http\Resources\Video\VideoCategoryResource;
use App\Api\V1\Http\Resources\Video\VideoResource;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Enums\Child\BornStatus;
use App\Enums\Package\PackageType;
use App\Enums\Package\PackageUserStatus;
use App\Enums\Video\VideoAccessType;
use App\Models\AgeGroup;
use App\Models\Child;
use App\Models\Video;
use App\Models\VideoCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @group Video Giáo dục
 */
class VideoController extends Controller
{
    use Response, UseLog;

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
            ->where('status', PackageUserStatus::Active)
            ->where('end_date', '>=', now())
            ->where('current_type', '!=', PackageType::Normal)
            ->exists();
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
            $childAgeMonths = null;
            $isUnborn = false;

            if ($request->filled('child_id')) {
                $user = auth('api')->user();
                if (!$user) {
                    return $this->jsonResponseError('Vui lòng đăng nhập để chọn hồ sơ của trẻ.', 401);
                }

                $child = $user->children()->find($request->input('child_id'));
                if (!$child) {
                    return $this->jsonResponseError('Không tìm thấy thông tin của trẻ hoặc bạn không có quyền truy cập hồ sơ này.', 404);
                }

                if ($child->is_born == BornStatus::Unborn) {
                    $isUnborn = true;
                } elseif ($child->birthday) {
                    $childAgeMonths = Carbon::parse($child->birthday)->diffInMonths(now());
                }
            } elseif ($request->filled('child_age_months')) {
                $childAgeMonths = (int) $request->input('child_age_months');
            }

            if ($isUnborn) {
                // Trẻ chưa sinh: Lấy nhóm Thai giáo và 2 nhóm đầu đời
                $prenatalGroup = AgeGroup::active()
                    ->whereNull('min_months')
                    ->whereNull('max_months')
                    ->first();

                $otherGroups = AgeGroup::active()
                    ->whereNotNull('min_months')
                    ->orderBy('min_months', 'asc')
                    ->take(2)
                    ->get();

                $groups = collect();
                if ($prenatalGroup) {
                    $prenatalGroup->is_current = true;
                    $groups->push($prenatalGroup);
                }
                foreach ($otherGroups as $g) {
                    $g->is_current = false;
                    $groups->push($g);
                }

                return $this->jsonResponseSuccess(AgeGroupResource::collection($groups));
            }

            if ($childAgeMonths !== null) {
                $nearestGroups = AgeGroup::nearestToChild($childAgeMonths)->get();

                // Xác định nhóm tuổi khớp nhất
                $hasCurrent = false;
                foreach ($nearestGroups as $g) {
                    $inRange = ($g->min_months === null || $g->min_months <= $childAgeMonths)
                        && ($g->max_months === null || $g->max_months >= $childAgeMonths);
                    if ($inRange && !$hasCurrent) {
                        $g->is_current = true;
                        $hasCurrent = true;
                    } else {
                        $g->is_current = false;
                    }
                }

                // Nếu không có nhóm nào chứa đúng tháng, gán nhóm gần nhất đầu tiên
                if (!$hasCurrent && $nearestGroups->isNotEmpty()) {
                    $nearestGroups->first()->is_current = true;
                }

                return $this->jsonResponseSuccess(AgeGroupResource::collection($nearestGroups));
            }

            // Mặc định: Trả về tất cả nhóm tuổi active sắp xếp theo sort_order và min_months
            $groups = AgeGroup::active()
                ->orderBy('sort_order', 'asc')
                ->orderByRaw('CASE WHEN min_months IS NULL THEN 0 ELSE 1 END')
                ->orderBy('min_months', 'asc')
                ->get();

            return $this->jsonResponseSuccess(AgeGroupResource::collection($groups));
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
            $query = VideoCategory::active()
                ->with('ageGroup')
                ->withCount([
                    'videos as videos_count' => fn ($q) => $q->active(),
                    'videos as free_videos_count' => fn ($q) => $q->active()->free(),
                    'videos as vip_videos_count' => fn ($q) => $q->active()->vip(),
                ]);

            if ($request->filled('age_group_id')) {
                $query->where('age_group_id', $request->input('age_group_id'));
            }

            $categories = $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->get();

            return $this->jsonResponseSuccess(VideoCategoryResource::collection($categories));
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
            $query = Video::active()->with('category');

            $categoryId = $request->input('video_category_id', $request->input('category_id'));
            if ($categoryId) {
                $query->where('video_category_id', $categoryId);
            }

            if ($request->filled('age_group_id')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('age_group_id', $request->input('age_group_id'));
                });
            }

            if ($request->filled('access_type')) {
                $query->where('access_type', $request->input('access_type'));
            }

            if ($request->filled('keyword')) {
                $query->where('title', 'like', '%' . $request->input('keyword') . '%');
            }

            $limit = max(1, min(50, (int) $request->input('limit', 15)));
            $videos = $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->paginate($limit);

            $isVip = $this->isVipUser();

            // Áp dụng quyền Free / VIP
            $videos->getCollection()->transform(function ($video) use ($isVip) {
                if ($isVip) {
                    $video->is_locked = false;
                } else {
                    if ($video->access_type == VideoAccessType::FREE) {
                        $video->is_locked = false;
                    } elseif ($video->access_type == VideoAccessType::VIP && $video->is_preview) {
                        $video->is_locked = false;
                    } else {
                        $video->is_locked = true;
                    }
                }
                return $video;
            });

            return $this->jsonResponseSuccess([
                'videos' => VideoResource::collection($videos),
                'pagination' => [
                    'current_page' => $videos->currentPage(),
                    'last_page' => $videos->lastPage(),
                    'per_page' => $videos->perPage(),
                    'total' => $videos->total(),
                ],
            ]);
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
            $video = Video::active()->with('category')->findOrFail($id);

            $isVip = $this->isVipUser();
            if ($isVip) {
                $video->is_locked = false;
            } else {
                if ($video->access_type == VideoAccessType::FREE) {
                    $video->is_locked = false;
                } elseif ($video->access_type == VideoAccessType::VIP && $video->is_preview) {
                    $video->is_locked = false;
                } else {
                    $video->is_locked = true;
                }
            }

            // Lấy 6 - 8 video liên quan trong cùng danh mục
            $relatedQuery = Video::active()
                ->where('id', '!=', $video->id);

            if ($video->video_category_id) {
                $relatedQuery->where('video_category_id', $video->video_category_id);
            }

            $relatedVideos = $relatedQuery->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            // Nếu cùng danh mục có ít hơn 4 video, lấy bổ sung các video cùng nhóm tuổi
            if ($relatedVideos->count() < 4 && $video->category?->age_group_id) {
                $ageGroupId = $video->category->age_group_id;
                $excludedIds = $relatedVideos->pluck('id')->push($video->id)->all();

                $moreVideos = Video::active()
                    ->whereNotIn('id', $excludedIds)
                    ->whereHas('category', function ($q) use ($ageGroupId) {
                        $q->where('age_group_id', $ageGroupId);
                    })
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->take(8 - $relatedVideos->count())
                    ->get();

                $relatedVideos = $relatedVideos->concat($moreVideos);
            }

            // Áp dụng quyền Free / VIP cho danh sách video liên quan
            $relatedVideos->transform(function ($item) use ($isVip) {
                if ($isVip) {
                    $item->is_locked = false;
                } else {
                    if ($item->access_type == VideoAccessType::FREE) {
                        $item->is_locked = false;
                    } elseif ($item->access_type == VideoAccessType::VIP && $item->is_preview) {
                        $item->is_locked = false;
                    } else {
                        $item->is_locked = true;
                    }
                }
                return $item;
            });

            return $this->jsonResponseSuccess([
                'video' => new VideoResource($video),
                'related_videos' => VideoResource::collection($relatedVideos),
            ]);
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
            $video = Video::active()->findOrFail($id);

            // Kiểm tra quyền xem nếu là video VIP
            if ($video->access_type == VideoAccessType::VIP && !$video->is_preview) {
                if (!$this->isVipUser()) {
                    return $this->jsonResponseError('Bạn không có quyền xem video này.', 403);
                }
            }

            // Deduplication theo Cache: 1 user/device chỉ được tính 1 view/video trong vòng 2 giờ
            $user = auth('api')->user();
            $identifier = $user ? "user_{$user->id}" : ('ip_' . md5($request->ip() . '_' . ($request->userAgent() ?? '')));
            $cacheKey = "video_view:{$video->id}:{$identifier}";

            if (!Cache::has($cacheKey)) {
                $video->increment('view_count');
                Cache::put($cacheKey, 1, now()->addHours(2));
            }

            return $this->jsonResponseSuccess([
                'id' => $video->id,
                'view_count' => (int) $video->view_count,
            ], __('Cập nhật lượt xem thành công.'));
        } catch (Exception $e) {
            $this->logError('Increment video view failed:', $e);
            return $this->jsonResponseError('Không tìm thấy video hoặc video đã bị ẩn', 404);
        }
    }
}
