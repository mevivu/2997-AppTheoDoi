<?php

namespace App\Api\V1\Http\Controllers\Memo;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Resources\Memo\MemoAgeConfigResource;
use App\Api\V1\Http\Resources\Memo\MemoGameDataResource;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Enums\ActiveStatus;
use App\Models\Child;
use App\Models\MemoAgeConfig;
use App\Models\MemoCard;
use App\Models\MemoTheme;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Memo Game API
 */
class MemoGameController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['getGuestGameData', 'getAgeConfigs']);
    }

    /**
     * Lấy danh sách cấu hình độ tuổi cho Memo Game
     */
    public function getAgeConfigs(): JsonResponse
    {
        try {
            $configs = MemoAgeConfig::where('status', ActiveStatus::Active->value)
                ->orderBy('min_age', 'asc')
                ->get();

            return $this->jsonResponseSuccess(MemoAgeConfigResource::collection($configs));
        } catch (Exception $e) {
            $this->logError('Lỗi lấy danh sách cấu hình độ tuổi memo', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy cấu hình độ tuổi.', 500);
        }
    }

    /**
     * Lấy dữ liệu trò chơi cho khách vãng lai / chơi thử (không cần đăng nhập, không cần child_id)
     */
    public function getGuestGameData(Request $request): JsonResponse
    {
        return $this->getGameData($request);
    }

    /**
     * Lấy dữ liệu trò chơi (thẻ bài) cho bài test Memo Game
     * Theme được chọn ngẫu nhiên theo hệ thống
     */
    public function getGameData(Request $request): JsonResponse
    {
        try {
            $ageConfigId = $request->input('age_config_id');
            $childId = $request->input('child_id');
            $themeId = $request->input('theme_id');

            // 1. Xác định Age Config
            $ageConfig = null;
            if ($ageConfigId) {
                $ageConfig = MemoAgeConfig::find($ageConfigId);
            }

            if (!$ageConfig && $childId) {
                $child = Child::find($childId);
                if ($child && $child->birthday) {
                    $age = Carbon::parse($child->birthday)->age;
                    $ageConfig = MemoAgeConfig::forAge($age)->first();
                }
            }

            if (!$ageConfig) {
                $ageConfig = MemoAgeConfig::where('status', ActiveStatus::Active->value)->first();
            }

            if (!$ageConfig) {
                return $this->jsonResponseError('Chưa có cấu hình độ tuổi cho Memo Game.', 404);
            }

            // 2. Xác định Theme: ưu tiên theo độ tuổi bé nếu có
            $theme = null;
            if ($themeId) {
                $theme = MemoTheme::find($themeId);
            }
            if (!$theme && isset($age) && $age > 0) {
                $theme = MemoTheme::where('status', ActiveStatus::Active->value)
                    ->where('age', $age)
                    ->inRandomOrder()
                    ->first();
            }
            if (!$theme) {
                $theme = MemoTheme::where('status', ActiveStatus::Active->value)->inRandomOrder()->first();
            }
            if (!$theme) {
                $theme = MemoTheme::first();
            }

            if (!$theme) {
                return $this->jsonResponseError('Chưa có chủ đề nào cho Memo Game.', 404);
            }

            // 3. Số cặp cần
            $neededPairs = $ageConfig->pairs_count ?: (int) floor(($ageConfig->rows * $ageConfig->columns) / 2);

            // 4. Lấy thẻ bài thật từ CSDL
            $realCards = MemoCard::where('memo_theme_id', $theme->id)
                ->where('status', ActiveStatus::Active->value)
                ->orderBy('position', 'asc')
                ->get();

            $availableCount = $realCards->count();
            $isFallback = false;
            $selectedCards = [];

            if ($availableCount >= $neededPairs) {
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
                        $clone = $selectedCards[count($selectedCards) % max(1, count($selectedCards))];
                        $selectedCards[] = array_merge($clone, [
                            'id' => 2000 + count($selectedCards),
                            'key' => 'clone_' . count($selectedCards),
                        ]);
                    }
                }
            }

            $payload = [
                'theme' => $theme,
                'age_config' => $ageConfig,
                'cards' => $selectedCards,
                'needed_pairs' => $neededPairs,
                'available_cards_count' => $availableCount,
                'is_fallback' => $isFallback,
            ];

            return $this->jsonResponseSuccess((new MemoGameDataResource($payload))->resolve());
        } catch (Exception $e) {
            $this->logError('Lỗi lấy dữ liệu Memo Game', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi tải màn chơi.', 500);
        }
    }

    /**
     * Danh sách thẻ ảo minh họa dự phòng cho từng chủ đề
     */
    private function getVirtualCardsPool(string $code): array
    {
        $code = strtolower($code);

        if (str_contains($code, 'veh') || str_contains($code, 'xe')) {
            return [
                ['name' => 'Ô tô con', 'icon' => 'ti ti-car', 'color' => '#dc2626'],
                ['name' => 'Xe buýt', 'icon' => 'ti ti-bus', 'color' => '#f59e0b'],
                ['name' => 'Xe cứu thương', 'icon' => 'ti ti-ambulance', 'color' => '#ef4444'],
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
