<?php

namespace App\Api\V1\Http\Controllers\Video;

use App\Admin\Http\Controllers\Controller;
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
     * @queryParam child_id integer ID của trẻ
     * @queryParam child_age_months integer Tuổi của trẻ theo số tháng
     *
     * @return JsonResponse
     */
    public function getAgeGroups(Request $request): JsonResponse
    {
        try {
            $childAgeMonths = null;
            $isUnborn = false;

            if ($request->filled('child_id')) {
                $child = Child::find($request->input('child_id'));
                if ($child) {
                    if ($child->is_born == BornStatus::Unborn) {
                        $isUnborn = true;
                    } elseif ($child->birthday) {
                        $childAgeMonths = Carbon::parse($child->birthday)->diffInMonths(now());
                    }
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
    public function getCategories(Request $request): JsonResponse
    {
        try {
            $query = VideoCategory::active()
                ->with('ageGroup')
                ->withCount('videos');

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
     * @queryParam keyword string Tìm kiếm theo tiêu đề
     * @queryParam page integer Trang hiện tại (mặc định 1)
     * @queryParam limit integer Số lượng trên trang (mặc định 15)
     *
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
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
     * Chi tiết Video (kèm phân quyền Free/VIP)
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

            return $this->jsonResponseSuccess(new VideoResource($video));
        } catch (Exception $e) {
            $this->logError('Get Video detail failed:', $e);
            return $this->jsonResponseError('Không tìm thấy video hoặc video đã bị ẩn', 404);
        }
    }

    /**
     * Tăng lượt xem video
     *
     * @param int $id
     * @return JsonResponse
     */
    public function incrementView($id): JsonResponse
    {
        try {
            $video = Video::findOrFail($id);
            $video->increment('view_count');

            return $this->jsonResponseSuccess([
                'id' => $video->id,
                'view_count' => (int) $video->view_count,
            ], __('Cập nhật lượt xem thành công.'));
        } catch (Exception $e) {
            $this->logError('Increment video view failed:', $e);
            return $this->jsonResponseError('Không thể cập nhật lượt xem', 404);
        }
    }
}
