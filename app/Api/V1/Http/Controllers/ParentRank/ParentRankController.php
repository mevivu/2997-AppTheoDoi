<?php

namespace App\Api\V1\Http\Controllers\ParentRank;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Support\Response;
use App\Enums\User\ParentRank;
use App\Models\ParentRankSnapshot;
use App\Services\ParentRank\ParentRankServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Phân hạng Bố mẹ (Parent Ranking)
 *
 * API cung cấp thông tin xếp hạng thành viên phụ huynh dựa trên thời gian sử dụng app,
 * tần suất, số bài đánh giá và chỉ số phát triển của con.
 */
class ParentRankController extends Controller
{
    use Response;

    protected ParentRankServiceInterface $parentRankService;

    public function __construct(ParentRankServiceInterface $parentRankService)
    {
        $this->parentRankService = $parentRankService;
        $this->middleware('auth:api')->except(['tiers']);
    }

    /**
     * Lấy Thứ Hạng và Tiến Độ Hiện Tại của Tôi
     *
     * Trả về thông tin phân hạng của phụ huynh trong tháng hiện tại:
     * cấp bậc, tổng điểm, các chỉ số thành phần và mục tiêu phấn đấu lên hạng kế tiếp.
     *
     * @authenticated
     */
    public function me(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        if (!$user) {
            return $this->jsonResponseError(__('Vui lòng đăng nhập.'), 401);
        }

        $progress = $this->parentRankService->getUserProgress($user);

        return $this->jsonResponseSuccess($progress, __('Lấy thông tin phân hạng thành công.'));
    }

    /**
     * Lịch Sử Phân Hạng Các Tháng Trước
     *
     * Lấy danh sách các kỳ phân hạng đã chốt trong quá khứ của người dùng.
     *
     * @authenticated
     * @queryParam limit int Số lượng tháng gần nhất cần lấy (mặc định: 6). Example: 6
     */
    public function history(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        if (!$user) {
            return $this->jsonResponseError(__('Vui lòng đăng nhập.'), 401);
        }

        $limit = max(1, min(24, (int) $request->input('limit', 6)));

        $snapshots = ParentRankSnapshot::where('user_id', $user->id)
            ->orderByDesc('period')
            ->limit($limit)
            ->get()
            ->map(function ($snap) {
                $rankEnum = $snap->rank instanceof ParentRank ? $snap->rank : ParentRank::from((int) $snap->rank);
                return [
                    'period' => $snap->period,
                    'rank' => [
                        'value' => $rankEnum->value,
                        'name' => $rankEnum->name(),
                        'color' => $rankEnum->colorHex(),
                        'icon' => $rankEnum->icon(),
                        'badge' => $rankEnum->badge(),
                    ],
                    'total_points' => (float) $snap->total_points,
                    'position' => $snap->position,
                    'is_final' => (bool) $snap->is_final,
                    'usage_minutes' => (int) $snap->usage_minutes,
                    'active_days' => (int) $snap->active_days,
                    'assessment_count' => (int) $snap->assessment_count,
                    'child_score_avg' => (float) $snap->child_score_avg,
                    'calculated_at' => $snap->calculated_at ? $snap->calculated_at->toDateTimeString() : null,
                ];
            });

        return $this->jsonResponseSuccess($snapshots, __('Lấy lịch sử phân hạng thành công.'));
    }

    /**
     * Danh Sách Tất Cả Cấp Bậc và Tiêu Chuẩn Phấn Đấu
     *
     * API công khai trả về thông tin tất cả các cấp bậc (Đồng, Bạc, Vàng, Kim Cương...)
     * cùng các ngưỡng điểm và trọng số cấu hình để hiển thị giao diện giới thiệu.
     */
    public function tiers(): JsonResponse
    {
        $config = $this->parentRankService->getConfig();
        $th = $config['thresholds'];

        $tiers = [];
        foreach (ParentRank::cases() as $case) {
            $minPoints = match ($case) {
                ParentRank::NewMember => 0.0,
                ParentRank::Bronze => (float) ($th['bronze'] ?? 20),
                ParentRank::Silver => (float) ($th['silver'] ?? 40),
                ParentRank::Gold => (float) ($th['gold'] ?? 60),
                ParentRank::Diamond => (float) ($th['diamond'] ?? 80),
            };

            $tiers[] = [
                'value' => $case->value,
                'name' => $case->name(),
                'color' => $case->colorHex(),
                'icon' => $case->icon(),
                'badge' => $case->badge(),
                'min_points' => $minPoints,
                'description' => match ($case) {
                    ParentRank::NewMember => 'Dành cho phụ huynh mới bắt đầu đồng hành cùng con.',
                    ParentRank::Bronze => 'Duy trì hoạt động và làm các bài kiểm tra cơ bản cho con.',
                    ParentRank::Silver => 'Tích cực tương tác, theo dõi sát sao sự phát triển của con.',
                    ParentRank::Gold => 'Phụ huynh gương mẫu, đồng hành toàn diện trong học tập và sức khỏe của con.',
                    ParentRank::Diamond => 'Cấp bậc danh dự cao nhất dành cho phụ huynh xuất sắc nhất tháng.',
                },
            ];
        }

        return $this->jsonResponseSuccess([
            'is_active' => $config['active'],
            'weights' => $config['weights'],
            'targets' => $config['targets'],
            'tiers' => $tiers,
        ], __('Lấy danh sách cấp bậc thành công.'));
    }
}
