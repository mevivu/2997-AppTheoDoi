<?php

namespace App\Api\V1\Services\Memo;

use App\Api\V1\Http\Resources\Memo\MemoGameDataResource;
use App\Enums\ActiveStatus;
use App\Models\MemoAgeConfig;
use App\Models\MemoCard;
use App\Models\MemoTheme;

class MemoGameBuilderService
{
    /**
     * Tạo danh sách N ván Memo Game với các chủ đề ngẫu nhiên khác nhau
     *
     * @param int $age Độ tuổi của bài kiểm tra
     * @param int|null $gamePlays Số lượng ván game cần tạo (mặc định lấy từ cấu hình độ tuổi: total_rounds)
     * @return array Danh sách payload các ván chơi đã format qua MemoGameDataResource
     */
    public static function buildRounds(int $age, ?int $gamePlays = null): array
    {
        // 1. Xác định Age Config theo đúng độ tuổi
        $ageConfig = MemoAgeConfig::forAge($age)
            ->where('status', ActiveStatus::Active->value)
            ->first();

        if (!$ageConfig) {
            // Fallback nếu độ tuổi chưa có cấu hình riêng: lấy cấu hình thấp nhất nếu age nhỏ hoặc cao nhất nếu age lớn
            $ageConfig = MemoAgeConfig::where('status', ActiveStatus::Active->value)
                ->iqTest()
                ->orderBy('min_age', 'asc')
                ->first();
        }

        if (!$ageConfig) {
            return [];
        }

        // Lấy số lượt chơi game từ cấu hình độ tuổi (total_rounds)
        if ($gamePlays === null || $gamePlays < 1) {
            $gamePlays = (int) ($ageConfig->total_rounds ?: 3);
        }

        // 2. Lấy các chủ đề đang active thuộc đúng độ tuổi của bài test
        $activeThemes = MemoTheme::where('status', ActiveStatus::Active->value)
            ->where('age', $age)
            ->inRandomOrder()
            ->get();

        // Fallback: nếu không có chủ đề theo đúng độ tuổi, lấy tất cả các chủ đề active đang có
        if ($activeThemes->isEmpty()) {
            $activeThemes = MemoTheme::where('status', ActiveStatus::Active->value)
                ->inRandomOrder()
                ->get();
        }

        if ($activeThemes->isEmpty()) {
            return [];
        }

        $rounds = [];
        $usedThemeIds = [];

        for ($i = 0; $i < $gamePlays; $i++) {
            // Chọn một chủ đề chưa được sử dụng trong bài test này
            $theme = $activeThemes->first(function ($t) use ($usedThemeIds) {
                return !in_array($t->id, $usedThemeIds);
            });

            // Nếu số ván vượt quá số chủ đề có sẵn, chọn ngẫu nhiên một chủ đề bất kỳ
            if (!$theme) {
                $theme = $activeThemes->random();
            }
            $usedThemeIds[] = $theme->id;

            $roundData = self::buildSingleRound($theme, $ageConfig);
            if ($roundData) {
                $rounds[] = (new MemoGameDataResource($roundData))->resolve();
            }
        }

        return $rounds;
    }

    /**
     * Tạo dữ liệu cho 1 ván chơi Memo Game cụ thể
     */
    public static function buildSingleRound(MemoTheme $theme, MemoAgeConfig $ageConfig): ?array
    {
        $neededPairs = $ageConfig->pairs_count ?: (int) floor(($ageConfig->rows * $ageConfig->columns) / 2);

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

            $virtualPool = self::getVirtualCardsPool($theme->code);
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

        return [
            'theme' => $theme,
            'age_config' => $ageConfig,
            'cards' => $selectedCards,
            'needed_pairs' => $neededPairs,
            'available_cards_count' => $availableCount,
            'is_fallback' => $isFallback,
        ];
    }

    /**
     * Thẻ ảo dự phòng
     */
    public static function getVirtualCardsPool(string $code): array
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
