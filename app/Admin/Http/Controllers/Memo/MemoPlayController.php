<?php

namespace App\Admin\Http\Controllers\Memo;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Memo\MemoPlayGetDataRequest;
use App\Admin\Http\Requests\Memo\MemoPlaySubmitResultRequest;
use App\Admin\Http\Resources\Memo\MemoPlayDataResource;
use App\Admin\Http\Resources\Memo\MemoPlayResultResource;
use App\Enums\ActiveStatus;
use App\Models\MemoAgeConfig;
use App\Models\MemoCard;
use App\Models\MemoRating;
use App\Models\MemoRatingRound;
use App\Models\MemoTheme;
use App\Traits\RouteAdminSystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MemoPlayController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Màn hình Chơi thử Memo Game
     */
    public function index()
    {
        $themes = MemoTheme::where('status', ActiveStatus::Active->value)
            ->withCount(['cards' => function ($query) {
                $query->where('status', ActiveStatus::Active->value);
            }])
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $ageConfigs = MemoAgeConfig::where('status', ActiveStatus::Active->value)
            ->orderBy('min_age', 'asc')
            ->get();

        return view('admin.memo-game.play.index', [
            'themes' => $themes,
            'ageConfigs' => $ageConfigs,
            'breadcrumbs' => $this->crums->add('Memo Game (Trí nhớ)', route(RouteAdminSystem::MEMO_THEME_INDEX))->add('Chơi thử Game'),
        ]);
    }

    /**
     * API Lấy dữ liệu trò chơi theo Chủ đề & Cấu hình Độ tuổi
     */
    public function getGameData(MemoPlayGetDataRequest $request): MemoPlayDataResource|JsonResponse
    {
        $themeId = (int) $request->input('theme_id');
        $ageConfigId = (int) $request->input('age_config_id');

        $theme = MemoTheme::find($themeId);
        if (!$theme) {
            $theme = MemoTheme::where('status', ActiveStatus::Active->value)->first();
        }

        $ageConfig = MemoAgeConfig::find($ageConfigId);
        if (!$ageConfig) {
            $ageConfig = MemoAgeConfig::where('status', ActiveStatus::Active->value)->first();
        }

        if (!$theme || !$ageConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy Chủ đề hoặc Cấu hình độ tuổi hợp lệ!',
            ], 404);
        }

        $neededPairs = $ageConfig->pairs_count ?: (int) floor(($ageConfig->rows * $ageConfig->columns) / 2);

        // Lấy danh sách thẻ bài thật từ CSDL
        $realCards = MemoCard::where('memo_theme_id', $theme->id)
            ->where('status', ActiveStatus::Active->value)
            ->orderBy('position', 'asc')
            ->get();

        $availableCount = $realCards->count();
        $isFallback = false;
        $selectedCards = [];

        if ($availableCount >= $neededPairs) {
            // Đủ số thẻ thật -> xáo trộn và lấy đúng số cặp cần
            $shuffled = $realCards->shuffle()->take($neededPairs);
            foreach ($shuffled as $card) {
                $selectedCards[] = [
                    'id' => $card->id,
                    'key' => 'real_' . $card->id,
                    'name' => $card->name,
                    'image' => asset($card->image),
                    'audio' => $card->audio ? asset($card->audio) : null,
                    'is_virtual' => false,
                ];
            }
        } else {
            // Chưa đủ thẻ thật: dùng cơ chế Smart Fallback (sử dụng các thẻ thật đã có + thẻ ảo minh họa)
            $isFallback = true;
            foreach ($realCards as $card) {
                $selectedCards[] = [
                    'id' => $card->id,
                    'key' => 'real_' . $card->id,
                    'name' => $card->name,
                    'image' => asset($card->image),
                    'audio' => $card->audio ? asset($card->audio) : null,
                    'is_virtual' => false,
                ];
            }

            // Sinh thêm các thẻ ảo phù hợp theo chủ đề nếu còn thiếu
            $virtualPool = $this->getVirtualCardsPool($theme->code);
            $virtualIndex = 0;

            while (count($selectedCards) < $neededPairs) {
                if (isset($virtualPool[$virtualIndex])) {
                    $item = $virtualPool[$virtualIndex];
                    $selectedCards[] = [
                        'id' => 1000 + $virtualIndex,
                        'key' => 'virtual_' . $virtualIndex,
                        'name' => $item['name'],
                        'image' => null,
                        'icon' => $item['icon'],
                        'color' => $item['color'],
                        'audio' => null,
                        'is_virtual' => true,
                    ];
                    $virtualIndex++;
                } else {
                    // Nếu hết pool ảo thì nhân bản lặp lại thẻ đã có
                    $clone = $selectedCards[count($selectedCards) % max(1, count($selectedCards))];
                    $selectedCards[] = array_merge($clone, [
                        'id' => 2000 + count($selectedCards),
                        'key' => 'clone_' . count($selectedCards),
                    ]);
                }
            }
        }

        return new MemoPlayDataResource([
            'theme' => $theme,
            'age_config' => $ageConfig,
            'cards' => $selectedCards,
            'needed_pairs' => $neededPairs,
            'available_cards_count' => $availableCount,
            'is_fallback' => $isFallback,
        ]);
    }

    /**
     * Lưu kết quả bài test chơi thử vào CSDL
     */
    public function submitResult(MemoPlaySubmitResultRequest $request): MemoPlayResultResource|JsonResponse
    {
        $theme = MemoTheme::find($request->theme_id);
        $ageConfig = MemoAgeConfig::find($request->age_config_id);

        $feedback = $this->generateFeedback(
            $request->evaluation_label,
            (float) $request->score,
            (int) $request->total_mistakes,
            (int) $request->total_duration_spent
        );

        DB::beginTransaction();
        try {
            $rating = MemoRating::create([
                'child_id' => null, // Bài chơi thử của quản trị viên
                'memo_theme_id' => $theme ? $theme->id : null,
                'memo_age_config_id' => $ageConfig ? $ageConfig->id : null,
                'age' => $ageConfig ? $ageConfig->min_age : 5,
                'total_duration_spent' => (int) $request->total_duration_spent,
                'total_pairs_matched' => (int) $request->total_pairs_matched,
                'total_mistakes' => (int) $request->total_mistakes,
                'score' => (float) $request->score,
                'evaluation_label' => $request->evaluation_label,
                'feedback' => $feedback,
                'status' => 'completed',
            ]);

            if ($request->has('rounds') && is_array($request->rounds)) {
                foreach ($request->rounds as $idx => $roundData) {
                    MemoRatingRound::create([
                        'memo_rating_id' => $rating->id,
                        'round_number' => $roundData['round_number'] ?? ($idx + 1),
                        'duration_spent' => (int) ($roundData['duration_spent'] ?? 0),
                        'pairs_matched' => (int) ($roundData['pairs_matched'] ?? 0),
                        'mistakes' => (int) ($roundData['mistakes'] ?? 0),
                        'score' => (float) ($roundData['score'] ?? 0),
                    ]);
                }
            }

            DB::commit();

            return new MemoPlayResultResource($rating);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu kết quả bài test: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Sinh nhận xét chuyên môn tự động
     */
    protected function generateFeedback(string $label, float $score, int $mistakes, int $duration): string
    {
        if ($score >= 85) {
            return "Bé có khả năng ghi nhớ thị giác và định vị không gian xuất sắc! Tốc độ nhận diện các cặp hình ảnh rất nhanh, chỉ mắc {$mistakes} lỗi trong suốt bài test. Khả năng tập trung và chuyển đổi chú ý đạt mức tối ưu.";
        } elseif ($score >= 70) {
            return "Khả năng quan sát và ghi nhớ của bé ở mức Tốt. Bé nhận diện mặt thẻ và liên kết hình ảnh khá chuẩn xác. Để tiến bộ hơn nữa, có thể tăng thử thách với các bộ thẻ nhiều chi tiết hơn.";
        } elseif ($score >= 50) {
            return "Bé hoàn thành bài test ở mức Khá. Có dấu hiệu mất tập trung nhẹ ở các lượt sau với {$mistakes} lần lật sai. Phụ huynh nên đồng hành, khích lệ bé chơi thường xuyên để nâng cao trí nhớ ngắn hạn.";
        } else {
            return "Bé cần thêm thời gian để làm quen với cấu trúc trò chơi và rèn luyện kỹ năng định vị hình ảnh. Nên bắt đầu từ mức độ Khởi động (2x3 thẻ) với các hình ảnh thật quen thuộc để tạo hứng thú cho bé.";
        }
    }

    /**
     * Thư viện thẻ ảo minh họa dự phòng (Fallback) khi CSDL chưa có đủ ảnh
     */
    protected function getVirtualCardsPool(string $themeCode): array
    {
        $code = strtolower($themeCode);

        if (str_contains($code, 'vehic') || str_contains($code, 'xe')) {
            return [
                ['name' => 'Xe cảnh sát', 'icon' => 'ti ti-police-car', 'color' => '#1e40af'],
                ['name' => 'Xe cứu hỏa', 'icon' => 'ti ti-firetruck', 'color' => '#b91c1c'],
                ['name' => 'Xe cứu thương', 'icon' => 'ti ti-ambulance', 'color' => '#dc2626'],
                ['name' => 'Xe buýt', 'icon' => 'ti ti-bus', 'color' => '#d97706'],
                ['name' => 'Xe taxi', 'icon' => 'ti ti-taxi', 'color' => '#ca8a04'],
                ['name' => 'Tàu hỏa', 'icon' => 'ti ti-train', 'color' => '#475569'],
                ['name' => 'Máy bay', 'icon' => 'ti ti-plane', 'color' => '#0284c7'],
                ['name' => 'Tàu thủy', 'icon' => 'ti ti-ship', 'color' => '#0891b2'],
                ['name' => 'Tên lửa', 'icon' => 'ti ti-rocket', 'color' => '#ea580c'],
                ['name' => 'Trực thăng', 'icon' => 'ti ti-helicopter', 'color' => '#059669'],
                ['name' => 'Xe đạp', 'icon' => 'ti ti-bike', 'color' => '#16a34a'],
                ['name' => 'Xe tải', 'icon' => 'ti ti-truck', 'color' => '#64748b'],
            ];
        }

        if (str_contains($code, 'flow') || str_contains($code, 'hoa')) {
            return [
                ['name' => 'Hoa hướng dương', 'icon' => 'ti ti-sun', 'color' => '#eab308'],
                ['name' => 'Hoa hồng', 'icon' => 'ti ti-flower', 'color' => '#e11d48'],
                ['name' => 'Hoa sen', 'icon' => 'ti ti-flower-lotus', 'color' => '#ec4899'],
                ['name' => 'Hoa cúc', 'icon' => 'ti ti-petal', 'color' => '#f59e0b'],
                ['name' => 'Hoa mai', 'icon' => 'ti ti-sparkles', 'color' => '#fbbf24'],
                ['name' => 'Hoa đào', 'icon' => 'ti ti-cherry', 'color' => '#f472b6'],
                ['name' => 'Hoa tulip', 'icon' => 'ti ti-plant-2', 'color' => '#ef4444'],
                ['name' => 'Hoa cẩm tú cầu', 'icon' => 'ti ti-circles', 'color' => '#06b6d4'],
                ['name' => 'Hoa đồng tiền', 'icon' => 'ti ti-coin', 'color' => '#f97316'],
                ['name' => 'Hoa lan', 'icon' => 'ti ti-leaf', 'color' => '#0d9488'],
            ];
        }

        if (str_contains($code, 'numb') || str_contains($code, 'so')) {
            return [
                ['name' => 'Số 0', 'icon' => 'ti ti-number-0', 'color' => '#0284c7'],
                ['name' => 'Số 1', 'icon' => 'ti ti-number-1', 'color' => '#3b82f6'],
                ['name' => 'Số 2', 'icon' => 'ti ti-number-2', 'color' => '#06b6d4'],
                ['name' => 'Số 3', 'icon' => 'ti ti-number-3', 'color' => '#10b981'],
                ['name' => 'Số 4', 'icon' => 'ti ti-number-4', 'color' => '#84cc16'],
                ['name' => 'Số 5', 'icon' => 'ti ti-number-5', 'color' => '#eab308'],
                ['name' => 'Số 6', 'icon' => 'ti ti-number-6', 'color' => '#f97316'],
                ['name' => 'Số 7', 'icon' => 'ti ti-number-7', 'color' => '#ef4444'],
                ['name' => 'Số 8', 'icon' => 'ti ti-number-8', 'color' => '#ec4899'],
                ['name' => 'Số 9', 'icon' => 'ti ti-number-9', 'color' => '#059669'],
            ];
        }

        // Flags & Default
        return [
            ['name' => 'Quốc kỳ Việt Nam', 'icon' => 'ti ti-flag-filled', 'color' => '#dc2626'],
            ['name' => 'Quốc kỳ Nhật Bản', 'icon' => 'ti ti-circle-filled', 'color' => '#ef4444'],
            ['name' => 'Quốc kỳ Hàn Quốc', 'icon' => 'ti ti-yin-yang', 'color' => '#2563eb'],
            ['name' => 'Quốc kỳ Mỹ', 'icon' => 'ti ti-flag', 'color' => '#1d4ed8'],
            ['name' => 'Quốc kỳ Anh', 'icon' => 'ti ti-cross', 'color' => '#1e40af'],
            ['name' => 'Quốc kỳ Pháp', 'icon' => 'ti ti-columns', 'color' => '#3b82f6'],
            ['name' => 'Quốc kỳ Đức', 'icon' => 'ti ti-palette', 'color' => '#eab308'],
            ['name' => 'Quốc kỳ Ý', 'icon' => 'ti ti-layout-columns', 'color' => '#16a34a'],
            ['name' => 'Quốc kỳ Nga', 'icon' => 'ti ti-building-castle', 'color' => '#0284c7'],
            ['name' => 'Quốc kỳ Lào', 'icon' => 'ti ti-world', 'color' => '#0891b2'],
        ];
    }
}
