<?php

namespace App\Admin\Services;

use App\Enums\Question\QuestionType;
use App\Models\ChildEvaluation;
use App\Models\ClassGrade;
use App\Models\FeatureUsage;
use App\Models\Journal;
use App\Models\Pregnancy;
use App\Models\Rating;
use App\Models\RatingPQ;
use App\Models\VaccinationSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FeatureStatisticsService
{
    /**
     * Get feature definitions catalog.
     */
    public static function getFeatureCatalog(): array
    {
        return [
            // Nhóm 1: Đánh giá toàn diện
            'pq' => [
                'name' => 'Chỉ số thể chất (PQ)',
                'short_name' => 'Thể chất (PQ)',
                'category' => 'evaluation',
                'category_name' => 'Đánh giá toàn diện',
                'icon' => 'ti ti-activity',
                'color' => '#3b82f6',
                'badge_class' => 'bg-blue-lt',
            ],
            'eq' => [
                'name' => 'Chỉ số cảm xúc (EQ)',
                'short_name' => 'Cảm xúc (EQ)',
                'category' => 'evaluation',
                'category_name' => 'Đánh giá toàn diện',
                'icon' => 'ti ti-heart-handshake',
                'color' => '#ec4899',
                'badge_class' => 'bg-pink-lt',
            ],
            'aq' => [
                'name' => 'Chỉ số vượt khó (AQ)',
                'short_name' => 'Vượt khó (AQ)',
                'category' => 'evaluation',
                'category_name' => 'Đánh giá toàn diện',
                'icon' => 'ti ti-mountain',
                'color' => '#10b981',
                'badge_class' => 'bg-green-lt',
            ],
            'iq' => [
                'name' => 'Chỉ số thông minh (IQ)',
                'short_name' => 'Thông minh (IQ)',
                'category' => 'evaluation',
                'category_name' => 'Đánh giá toàn diện',
                'icon' => 'ti ti-bulb',
                'color' => '#f59e0b',
                'badge_class' => 'bg-warning-lt',
            ],
            'gpa' => [
                'name' => 'Học bạ điện tử (GPA)',
                'short_name' => 'Học bạ (GPA)',
                'category' => 'evaluation',
                'category_name' => 'Đánh giá toàn diện',
                'icon' => 'ti ti-school',
                'color' => '#8b5cf6',
                'badge_class' => 'bg-purple-lt',
            ],

            // Nhóm 2: Tiện ích
            'predict_height' => [
                'name' => 'Dự đoán chiều cao',
                'short_name' => 'Dự đoán chiều cao',
                'category' => 'utility',
                'category_name' => 'Tiện ích',
                'icon' => 'ti ti-ruler-measure',
                'color' => '#06b6d4',
                'badge_class' => 'bg-cyan-lt',
            ],
            'vaccine' => [
                'name' => 'Theo dõi lịch tiêm chủng',
                'short_name' => 'Tiêm chủng',
                'category' => 'utility',
                'category_name' => 'Tiện ích',
                'icon' => 'ti ti-needle',
                'color' => '#14b8a6',
                'badge_class' => 'bg-teal-lt',
            ],
            'pregnancy' => [
                'name' => 'Theo dõi thai kỳ',
                'short_name' => 'Thai kỳ',
                'category' => 'utility',
                'category_name' => 'Tiện ích',
                'icon' => 'ti ti-baby-carriage',
                'color' => '#f43f5e',
                'badge_class' => 'bg-danger-lt',
            ],
            'diary_prescription' => [
                'name' => 'Hồ sơ y tế',
                'short_name' => 'Hồ sơ y tế',
                'category' => 'utility',
                'category_name' => 'Tiện ích',
                'icon' => 'ti ti-file-medical',
                'color' => '#6366f1',
                'badge_class' => 'bg-indigo-lt',
            ],
            'diary_moment' => [
                'name' => 'Nhật ký khoảnh khắc',
                'short_name' => 'Nhật ký',
                'category' => 'utility',
                'category_name' => 'Tiện ích',
                'icon' => 'ti ti-camera',
                'color' => '#f97316',
                'badge_class' => 'bg-orange-lt',
            ],
            'develop' => [
                'name' => 'Quá trình phát triển',
                'short_name' => 'Phát triển',
                'category' => 'utility',
                'category_name' => 'Tiện ích',
                'icon' => 'ti ti-chart-arrows',
                'color' => '#84cc16',
                'badge_class' => 'bg-lime-lt',
            ],
            'store' => [
                'name' => 'Cửa hàng',
                'short_name' => 'Cửa hàng',
                'category' => 'utility',
                'category_name' => 'Tiện ích',
                'icon' => 'ti ti-shopping-cart',
                'color' => '#0ea5e9',
                'badge_class' => 'bg-azure-lt',
            ],
            'clinic' => [
                'name' => 'Tìm phòng khám',
                'short_name' => 'Phòng khám',
                'category' => 'utility',
                'category_name' => 'Tiện ích',
                'icon' => 'ti ti-building-hospital',
                'color' => '#64748b',
                'badge_class' => 'bg-secondary-lt',
            ],
        ];
    }

    /**
     * Resolve date range from period string or custom inputs.
     */
    public function resolveDateRange(string $period = '30d', ?string $from = null, ?string $to = null): array
    {
        $now = Carbon::now();

        if ($period === 'custom' && $from && $to) {
            $startDate = Carbon::parse($from)->startOfDay();
            $endDate = Carbon::parse($to)->endOfDay();
        } elseif ($period === '1d' || $period === 'today') {
            $startDate = $now->copy()->startOfDay();
            $endDate = $now->copy()->endOfDay();
        } elseif ($period === 'yesterday') {
            $startDate = $now->copy()->subDay()->startOfDay();
            $endDate = $now->copy()->subDay()->endOfDay();
        } elseif ($period === '7d' || $period === 'week') {
            $startDate = $now->copy()->subDays(6)->startOfDay();
            $endDate = $now->copy()->endOfDay();
        } elseif ($period === 'this_week') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
        } elseif ($period === 'this_month') {
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
        } elseif ($period === 'last_month') {
            $startDate = $now->copy()->subMonth()->startOfMonth();
            $endDate = $now->copy()->subMonth()->endOfMonth();
        } elseif ($period === '90d' || $period === '3m') {
            $startDate = $now->copy()->subDays(89)->startOfDay();
            $endDate = $now->copy()->endOfDay();
        } elseif ($period === 'all') {
            $startDate = Carbon::parse('2024-01-01')->startOfDay();
            $endDate = $now->copy()->endOfDay();
        } else {
            // Default 30d
            $startDate = $now->copy()->subDays(29)->startOfDay();
            $endDate = $now->copy()->endOfDay();
        }

        // Calculate previous comparison period with identical duration
        $durationInDays = max(1, $startDate->diffInDays($endDate));
        $prevEndDate = $startDate->copy()->subSecond();
        $prevStartDate = $prevEndDate->copy()->subDays($durationInDays)->startOfDay();

        return [
            'start' => $startDate,
            'end' => $endDate,
            'prev_start' => $prevStartDate,
            'prev_end' => $prevEndDate,
            'days_count' => $durationInDays + 1,
        ];
    }

    /**
     * Get aggregated feature statistics report.
     */
    public function getStatistics(string $period = '30d', string $category = 'evaluation', ?string $from = null, ?string $to = null): array
    {
        $dates = $this->resolveDateRange($period, $from, $to);
        $catalog = self::getFeatureCatalog();

        // Filter catalog by category if requested
        if ($category !== 'all') {
            $catalog = array_filter($catalog, fn($item) => $item['category'] === $category);
        }

        // 1. Gather counts & unique entities for current period
        $currentStats = $this->gatherRawStats($dates['start'], $dates['end'], array_keys($catalog));
        
        // 2. Gather counts for previous period to calculate growth
        $prevStats = $this->gatherRawStats($dates['prev_start'], $dates['prev_end'], array_keys($catalog));

        // 3. Build ranking list
        $totalUsages = 0;
        $prevTotalUsages = 0;
        $uniqueUsersSet = [];
        $uniqueChildrenSet = [];
        $rankingList = [];

        foreach ($catalog as $code => $meta) {
            $curr = $currentStats[$code] ?? ['count' => 0, 'users' => [], 'children' => []];
            $prev = $prevStats[$code] ?? ['count' => 0, 'users' => [], 'children' => []];

            $count = $curr['count'];
            $prevCount = $prev['count'];
            $totalUsages += $count;
            $prevTotalUsages += $prevCount;

            foreach ($curr['users'] as $u) {
                $uniqueUsersSet[$u] = true;
            }
            foreach ($curr['children'] as $c) {
                $uniqueChildrenSet[$c] = true;
            }

            // Calculate growth percentage
            $growth = 0.0;
            if ($prevCount > 0) {
                $growth = round((($count - $prevCount) / $prevCount) * 100, 1);
            } elseif ($count > 0) {
                $growth = 100.0;
            }

            $rankingList[] = [
                'code' => $code,
                'name' => $meta['name'],
                'short_name' => $meta['short_name'],
                'category' => $meta['category'],
                'category_name' => $meta['category_name'],
                'icon' => $meta['icon'],
                'color' => $meta['color'],
                'badge_class' => $meta['badge_class'],
                'count' => $count,
                'prev_count' => $prevCount,
                'unique_users' => count($curr['users']),
                'unique_children' => count($curr['children']),
                'growth' => $growth,
                'percentage' => 0.0, // will compute after total
            ];
        }

        // Compute percentages and sort by count desc
        usort($rankingList, fn($a, $b) => $b['count'] <=> $a['count']);

        foreach ($rankingList as &$item) {
            $item['percentage'] = $totalUsages > 0 ? round(($item['count'] / $totalUsages) * 100, 1) : 0.0;
        }
        unset($item);

        // Overall growth rate
        $overallGrowth = 0.0;
        if ($prevTotalUsages > 0) {
            $overallGrowth = round((($totalUsages - $prevTotalUsages) / $prevTotalUsages) * 100, 1);
        } elseif ($totalUsages > 0) {
            $overallGrowth = 100.0;
        }

        // 4. Build Trend Timeline Data for amCharts
        $trendData = $this->buildTrendTimeline($dates['start'], $dates['end'], $period, $catalog);

        // 5. Build Donut Chart Data
        $donutData = [];
        foreach ($rankingList as $item) {
            if ($item['count'] > 0) {
                $donutData[] = [
                    'category' => $item['short_name'],
                    'value' => $item['count'],
                    'color' => $item['color'],
                    'percentage' => $item['percentage'] . '%',
                ];
            }
        }

        // Top 1 Feature
        $topFeature = !empty($rankingList) && $rankingList[0]['count'] > 0 ? $rankingList[0] : null;

        return [
            'period' => $period,
            'category' => $category,
            'date_range_label' => $dates['start']->format('d/m/Y') . ' - ' . $dates['end']->format('d/m/Y'),
            'kpis' => [
                'total_usages' => $totalUsages,
                'prev_total_usages' => $prevTotalUsages,
                'growth' => $overallGrowth,
                'unique_users_count' => count($uniqueUsersSet),
                'unique_children_count' => count($uniqueChildrenSet),
                'top_feature' => $topFeature,
            ],
            'ranking' => $rankingList,
            'trend_data' => $trendData['data'],
            'trend_series' => $trendData['series'],
            'donut_data' => $donutData,
        ];
    }

    /**
     * Gather raw counts and unique user/child ids for a specific date range.
     */
    protected function gatherRawStats(Carbon $startDate, Carbon $endDate, array $allowedCodes): array
    {
        $stats = [];
        foreach ($allowedCodes as $code) {
            $stats[$code] = [
                'count' => 0,
                'users' => [],
                'children' => [],
            ];
        }

        // 1. Chỉ số cảm xúc (EQ) - ratings where type = 'eq'
        if (in_array('eq', $allowedCodes)) {
            $eqRecords = Rating::with('child:id,user_id')
                ->where('type', QuestionType::EQ)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get(['id', 'child_id', 'created_at']);

            $stats['eq']['count'] += $eqRecords->count();
            foreach ($eqRecords as $r) {
                if ($r->child_id) {
                    $stats['eq']['children'][$r->child_id] = true;
                    if ($r->child && $r->child->user_id) {
                        $stats['eq']['users'][$r->child->user_id] = true;
                    }
                }
            }
        }

        // 2. Chỉ số vượt khó (AQ) - ratings where type = 'aq'
        if (in_array('aq', $allowedCodes)) {
            $aqRecords = Rating::with('child:id,user_id')
                ->where('type', QuestionType::AQ)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get(['id', 'child_id', 'created_at']);

            $stats['aq']['count'] += $aqRecords->count();
            foreach ($aqRecords as $r) {
                if ($r->child_id) {
                    $stats['aq']['children'][$r->child_id] = true;
                    if ($r->child && $r->child->user_id) {
                        $stats['aq']['users'][$r->child->user_id] = true;
                    }
                }
            }
        }

        // 3. Chỉ số thông minh (IQ) - ratings where type = 'iq'
        if (in_array('iq', $allowedCodes)) {
            $iqRecords = Rating::with('child:id,user_id')
                ->where('type', QuestionType::IQ)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get(['id', 'child_id', 'created_at']);

            $stats['iq']['count'] += $iqRecords->count();
            foreach ($iqRecords as $r) {
                if ($r->child_id) {
                    $stats['iq']['children'][$r->child_id] = true;
                    if ($r->child && $r->child->user_id) {
                        $stats['iq']['users'][$r->child->user_id] = true;
                    }
                }
            }
        }

        // 4. Chỉ số thể chất (PQ) - ratings_pqs
        if (in_array('pq', $allowedCodes)) {
            $pqRecords = RatingPQ::with('child:id,user_id')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get(['id', 'child_id', 'created_at']);

            $stats['pq']['count'] += $pqRecords->count();
            foreach ($pqRecords as $r) {
                if ($r->child_id) {
                    $stats['pq']['children'][$r->child_id] = true;
                    if ($r->child && $r->child->user_id) {
                        $stats['pq']['users'][$r->child->user_id] = true;
                    }
                }
            }
        }

        // 5. Học bạ điện tử (GPA) - class_grades
        if (in_array('gpa', $allowedCodes)) {
            $gpaRecords = ClassGrade::with('children:id,user_id')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get(['id', 'child_id', 'created_at']);

            $stats['gpa']['count'] += $gpaRecords->count();
            foreach ($gpaRecords as $r) {
                if ($r->child_id) {
                    $stats['gpa']['children'][$r->child_id] = true;
                    if ($r->children && $r->children->user_id) {
                        $stats['gpa']['users'][$r->children->user_id] = true;
                    }
                }
            }
        }

        // 6. Tiêm chủng - vaccination_schedules
        if (in_array('vaccine', $allowedCodes)) {
            $vacRecords = VaccinationSchedule::whereBetween('created_at', [$startDate, $endDate])
                ->get(['id', 'child_id', 'user_id', 'created_at']);

            $stats['vaccine']['count'] += $vacRecords->count();
            foreach ($vacRecords as $r) {
                if ($r->child_id) $stats['vaccine']['children'][$r->child_id] = true;
                if ($r->user_id) $stats['vaccine']['users'][$r->user_id] = true;
            }
        }

        // 7. Thai kỳ - pregnancies
        if (in_array('pregnancy', $allowedCodes)) {
            $pregRecords = Pregnancy::whereBetween('created_at', [$startDate, $endDate])
                ->get(['id', 'user_id', 'created_at']);

            $stats['pregnancy']['count'] += $pregRecords->count();
            foreach ($pregRecords as $r) {
                if ($r->user_id) $stats['pregnancy']['users'][$r->user_id] = true;
            }
        }

        // 8. Hồ sơ y tế - journals (prescription)
        if (in_array('diary_prescription', $allowedCodes)) {
            $presRecords = Journal::where('type', 'prescription')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get(['id', 'child_id', 'created_at']);

            $stats['diary_prescription']['count'] += $presRecords->count();
            foreach ($presRecords as $r) {
                if ($r->child_id) $stats['diary_prescription']['children'][$r->child_id] = true;
            }
        }

        // 9. Nhật ký khoảnh khắc - journals (moment)
        if (in_array('diary_moment', $allowedCodes)) {
            $momRecords = Journal::where('type', 'moment')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get(['id', 'child_id', 'created_at']);

            $stats['diary_moment']['count'] += $momRecords->count();
            foreach ($momRecords as $r) {
                if ($r->child_id) $stats['diary_moment']['children'][$r->child_id] = true;
            }
        }

        // 10. Tích hợp thêm từ bảng feature_usages (cho các lượt truy cập hoặc tính năng không tạo bảng riêng)
        $featureUsages = FeatureUsage::whereIn('feature_code', $allowedCodes)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['id', 'user_id', 'child_id', 'feature_code', 'action']);

        foreach ($featureUsages as $fu) {
            $code = $fu->feature_code;
            if (isset($stats[$code])) {
                // For utilities that don't have separate DB tables (like predict_height, develop, store, clinic)
                // or if action is 'view', accumulate usage count
                if (in_array($code, ['predict_height', 'develop', 'store', 'clinic']) || $fu->action === 'view') {
                    $stats[$code]['count']++;
                    if ($fu->user_id) $stats[$code]['users'][$fu->user_id] = true;
                    if ($fu->child_id) $stats[$code]['children'][$fu->child_id] = true;
                }
            }
        }

        // Convert user and child sets to indexed arrays
        foreach ($stats as $code => &$val) {
            $val['users'] = array_keys($val['users']);
            $val['children'] = array_keys($val['children']);
        }
        unset($val);

        return $stats;
    }

    /**
     * Build time series data points for amCharts 5.
     */
    protected function buildTrendTimeline(Carbon $startDate, Carbon $endDate, string $period, array $catalog): array
    {
        $data = [];
        $series = [];

        foreach ($catalog as $code => $meta) {
            $series[] = [
                'field' => $code,
                'name' => $meta['short_name'],
                'color' => $meta['color'],
            ];
        }

        $days = $startDate->diffInDays($endDate);

        if ($period === '1d' || $period === 'today' || $days <= 1) {
            // Group by hour (0h -> 23h)
            for ($h = 0; $h < 24; $h++) {
                $pointStart = $startDate->copy()->addHours($h);
                $pointEnd = $pointStart->copy()->addHour()->subSecond();
                $pointLabel = sprintf('%02dh', $h);

                $row = ['date' => $pointLabel];
                $subStats = $this->gatherRawStats($pointStart, $pointEnd, array_keys($catalog));
                foreach ($catalog as $code => $meta) {
                    $row[$code] = $subStats[$code]['count'] ?? 0;
                }
                $data[] = $row;
            }
        } elseif ($days <= 31) {
            // Group by day (e.g. 01/09, 02/09...)
            for ($i = 0; $i <= $days; $i++) {
                $pointDate = $startDate->copy()->addDays($i);
                $pointStart = $pointDate->copy()->startOfDay();
                $pointEnd = $pointDate->copy()->endOfDay();
                $pointLabel = $pointDate->format('d/m');

                $row = ['date' => $pointLabel];
                $subStats = $this->gatherRawStats($pointStart, $pointEnd, array_keys($catalog));
                foreach ($catalog as $code => $meta) {
                    $row[$code] = $subStats[$code]['count'] ?? 0;
                }
                $data[] = $row;
            }
        } else {
            // Group by week or month for longer durations
            $currentCursor = $startDate->copy()->startOfWeek();
            while ($currentCursor->lte($endDate)) {
                $pointEnd = $currentCursor->copy()->endOfWeek();
                if ($pointEnd->gt($endDate)) {
                    $pointEnd = $endDate->copy();
                }

                $pointLabel = 'T' . $currentCursor->format('W') . ' (' . $currentCursor->format('d/m') . ')';
                $row = ['date' => $pointLabel];
                $subStats = $this->gatherRawStats($currentCursor, $pointEnd, array_keys($catalog));
                foreach ($catalog as $code => $meta) {
                    $row[$code] = $subStats[$code]['count'] ?? 0;
                }
                $data[] = $row;
                $currentCursor->addWeek();
            }
        }

        return [
            'data' => $data,
            'series' => $series,
        ];
    }
}
