<?php

namespace App\Api\V1\Services\Video;

use App\Api\V1\Repositories\AgeGroup\AgeGroupRepositoryInterface;
use App\Api\V1\Repositories\Video\VideoRepositoryInterface;
use App\Api\V1\Repositories\VideoCategory\VideoCategoryRepositoryInterface;
use App\Enums\Child\BornStatus;
use App\Enums\Package\PackageType;
use App\Enums\Package\PackageUserStatus;
use App\Enums\Video\VideoAccessType;
use App\Models\FeatureUsage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VideoService implements VideoServiceInterface
{
    protected VideoRepositoryInterface $videoRepository;
    protected AgeGroupRepositoryInterface $ageGroupRepository;
    protected VideoCategoryRepositoryInterface $videoCategoryRepository;

    public function __construct(
        VideoRepositoryInterface $videoRepository,
        AgeGroupRepositoryInterface $ageGroupRepository,
        VideoCategoryRepositoryInterface $videoCategoryRepository
    ) {
        $this->videoRepository = $videoRepository;
        $this->ageGroupRepository = $ageGroupRepository;
        $this->videoCategoryRepository = $videoCategoryRepository;
    }

    /**
     * Kiểm tra user hiện tại có gói VIP đang hoạt động hay không
     * (Gói dùng thử - Trial và gói thường - Normal không được coi là VIP)
     */
    public function isVipUser(): bool
    {
        $user = auth('api')->user();
        if (!$user) {
            return false;
        }

        return $user->userPackages()
            ->where('status', PackageUserStatus::Active)
            ->where('end_date', '>=', now())
            ->whereNotIn('current_type', [
                PackageType::Normal->value,
                PackageType::Trial->value,
                PackageType::Normal,
                PackageType::Trial,
            ])
            ->exists();
    }

    /**
     * Lấy danh sách nhóm độ tuổi (3 nhóm gần nhất nếu có child_id hoặc child_age_months)
     */
    public function getAgeGroups(Request $request): Collection
    {
        $childAgeMonths = null;
        $isUnborn = false;

        if ($request->filled('child_id')) {
            $user = auth('api')->user();
            if (!$user) {
                throw new HttpException(401, 'Vui lòng đăng nhập để chọn hồ sơ của trẻ.');
            }

            $child = $user->children()->find($request->input('child_id'));
            if (!$child) {
                throw new HttpException(404, 'Không tìm thấy thông tin của trẻ hoặc bạn không có quyền truy cập hồ sơ này.');
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
            $prenatalGroup = $this->ageGroupRepository->getPrenatalGroup();
            $otherGroups = $this->ageGroupRepository->getEarlyStageGroups(2);

            $groups = new Collection();
            if ($prenatalGroup) {
                $prenatalGroup->is_current = true;
                $groups->push($prenatalGroup);
            }
            foreach ($otherGroups as $g) {
                $g->is_current = false;
                $groups->push($g);
            }

            return $groups;
        }

        if ($childAgeMonths !== null) {
            $nearestIds = $this->ageGroupRepository->getNearestGroupIds($childAgeMonths, 3);
            $nearestGroups = $this->ageGroupRepository->getByIdsOrdered($nearestIds);

            $hasCurrent = false;
            foreach ($nearestGroups as $g) {
                $inRange = ($g->min_months === null || $g->min_months <= $childAgeMonths)
                    && ($g->max_months === null || $g->max_months >= $childAgeMonths);
                if ($inRange && !$hasCurrent && ($g->min_months !== null || $g->max_months !== null)) {
                    $g->is_current = true;
                    $hasCurrent = true;
                } else {
                    $g->is_current = false;
                }
            }

            if (!$hasCurrent && $nearestGroups->isNotEmpty() && !empty($nearestIds)) {
                $closestId = $nearestIds[0];
                foreach ($nearestGroups as $g) {
                    if ($g->id == $closestId) {
                        $g->is_current = true;
                        break;
                    }
                }
            }

            return $nearestGroups;
        }

        return $this->ageGroupRepository->getAllActiveOrdered();
    }

    /**
     * Lấy danh mục video theo nhóm tuổi kèm số lượng video
     */
    public function getCategories(Request $request): Collection
    {
        $ageGroupId = $request->filled('age_group_id') ? (int) $request->input('age_group_id') : null;
        return $this->videoCategoryRepository->getCategoriesWithCounts($ageGroupId);
    }

    /**
     * Lấy danh sách video phân trang kèm kiểm tra quyền VIP/Free
     */
    public function getVideoList(Request $request): array
    {
        $filters = [
            'video_category_id' => $request->input('video_category_id', $request->input('category_id')),
            'age_group_id' => $request->input('age_group_id'),
            'access_type' => $request->input('access_type'),
            'keyword' => $request->input('keyword'),
        ];

        $limit = max(1, min(50, (int) $request->input('limit', 15)));
        $videos = $this->videoRepository->getVideoList($filters, $limit);

        $isVip = $this->isVipUser();

        $videos->getCollection()->transform(function ($video) use ($isVip) {
            $video->is_locked = $this->computeIsLocked($video, $isVip);
            return $video;
        });

        return [
            'videos' => $videos,
            'pagination' => [
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
                'per_page' => $videos->perPage(),
                'total' => $videos->total(),
            ],
        ];
    }

    /**
     * Lấy chi tiết video và danh sách video liên quan
     */
    public function getVideoDetail(int $id): array
    {
        $video = $this->videoRepository->findActiveWithCategory($id);
        if (!$video) {
            throw new HttpException(404, 'Không tìm thấy video hoặc video đã bị ẩn');
        }

        $isVip = $this->isVipUser();
        $video->is_locked = $this->computeIsLocked($video, $isVip);

        $relatedVideos = $this->videoRepository->getRelatedVideos(
            $video->id,
            $video->video_category_id,
            $video->category?->age_group_id,
            8
        );

        $relatedVideos->transform(function ($item) use ($isVip) {
            $item->is_locked = $this->computeIsLocked($item, $isVip);
            return $item;
        });

        return [
            'video' => $video,
            'related_videos' => $relatedVideos,
        ];
    }

    /**
     * Tăng lượt xem video kèm bảo vệ chống spam (2 giờ / user)
     */
    public function incrementView(Request $request, int $id): array
    {
        $video = $this->videoRepository->findActiveWithCategory($id);
        if (!$video) {
            throw new HttpException(404, 'Không tìm thấy video hoặc video đã bị ẩn');
        }

        if ($video->access_type == VideoAccessType::VIP && !$video->is_preview) {
            if (!$this->isVipUser()) {
                throw new HttpException(403, 'Bạn không có quyền xem video này.');
            }
        }

        $user = auth('api')->user();
        $identifier = $user ? "user_{$user->id}" : ('ip_' . md5($request->ip() . '_' . ($request->userAgent() ?? '')));
        $cacheKey = "video_view:{$video->id}:{$identifier}";

        if (!Cache::has($cacheKey)) {
            $this->videoRepository->incrementViewCount($video);
            $video->refresh();
            Cache::put($cacheKey, 1, now()->addHours(2));

            // Ghi nhận sự kiện thống kê chức năng Video giáo dục
            if (Schema::hasTable('feature_usages')) {
                FeatureUsage::create([
                    'user_id' => $user?->id,
                    'child_id' => $request->input('child_id'),
                    'feature_code' => 'video_education',
                    'feature_name' => 'Video giáo dục',
                    'category' => 'education',
                    'action' => 'watch_video',
                    'metadata' => json_encode([
                        'video_id' => $video->id,
                        'title' => $video->title,
                    ]),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return [
            'id' => $video->id,
            'view_count' => (int) $video->view_count,
        ];
    }

    /**
     * Tính toán trạng thái khóa video cho user hiện tại
     */
    protected function computeIsLocked($video, bool $isVip): bool
    {
        if ($isVip) {
            return false;
        }

        if ($video->access_type == VideoAccessType::FREE) {
            return false;
        }

        if ($video->access_type == VideoAccessType::VIP && $video->is_preview) {
            return false;
        }

        return true;
    }
}
