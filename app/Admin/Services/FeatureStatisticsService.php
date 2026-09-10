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
            'start_date' => $dates['start']->format('Y-m-d'),
            'end_date' => $dates['end']->format('Y-m-d'),
            'start_date_formatted' => $dates['start']->format('d/m/Y'),
            'end_date_formatted' => $dates['end']->format('d/m/Y'),
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
     * Uses lightweight direct DB aggregations to avoid hydrating tens of thousands of Eloquent models.
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
            $eqAgg = DB::table('ratings')
                ->where('type', QuestionType::EQ->value ?? 'eq')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('COUNT(*) as total')
                ->first();
            $stats['eq']['count'] += (int) ($eqAgg->total ?? 0);

            $stats['eq']['children'] = DB::table('ratings')
                ->where('type', QuestionType::EQ->value ?? 'eq')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('child_id')
                ->distinct()
                ->pluck('child_id')
                ->toArray();

            if (!empty($stats['eq']['children'])) {
                $stats['eq']['users'] = DB::table('children')
                    ->whereIn('id', $stats['eq']['children'])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        // 2. Chỉ số vượt khó (AQ) - ratings where type = 'aq'
        if (in_array('aq', $allowedCodes)) {
            $aqAgg = DB::table('ratings')
                ->where('type', QuestionType::AQ->value ?? 'aq')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('COUNT(*) as total')
                ->first();
            $stats['aq']['count'] += (int) ($aqAgg->total ?? 0);

            $stats['aq']['children'] = DB::table('ratings')
                ->where('type', QuestionType::AQ->value ?? 'aq')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('child_id')
                ->distinct()
                ->pluck('child_id')
                ->toArray();

            if (!empty($stats['aq']['children'])) {
                $stats['aq']['users'] = DB::table('children')
                    ->whereIn('id', $stats['aq']['children'])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        // 3. Chỉ số thông minh (IQ) - ratings where type = 'iq'
        if (in_array('iq', $allowedCodes)) {
            $iqAgg = DB::table('ratings')
                ->where('type', QuestionType::IQ->value ?? 'iq')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('COUNT(*) as total')
                ->first();
            $stats['iq']['count'] += (int) ($iqAgg->total ?? 0);

            $stats['iq']['children'] = DB::table('ratings')
                ->where('type', QuestionType::IQ->value ?? 'iq')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('child_id')
                ->distinct()
                ->pluck('child_id')
                ->toArray();

            if (!empty($stats['iq']['children'])) {
                $stats['iq']['users'] = DB::table('children')
                    ->whereIn('id', $stats['iq']['children'])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        // 4. Chỉ số thể chất (PQ) - ratings_pqs
        if (in_array('pq', $allowedCodes)) {
            $pqAgg = DB::table('ratings_pqs')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('COUNT(*) as total')
                ->first();
            $stats['pq']['count'] += (int) ($pqAgg->total ?? 0);

            $stats['pq']['children'] = DB::table('ratings_pqs')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('child_id')
                ->distinct()
                ->pluck('child_id')
                ->toArray();

            if (!empty($stats['pq']['children'])) {
                $stats['pq']['users'] = DB::table('children')
                    ->whereIn('id', $stats['pq']['children'])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        // 5. Học bạ điện tử (GPA) - class_grades
        if (in_array('gpa', $allowedCodes)) {
            $gpaAgg = DB::table('class_grades')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('COUNT(*) as total')
                ->first();
            $stats['gpa']['count'] += (int) ($gpaAgg->total ?? 0);

            $stats['gpa']['children'] = DB::table('class_grades')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('child_id')
                ->distinct()
                ->pluck('child_id')
                ->toArray();

            if (!empty($stats['gpa']['children'])) {
                $stats['gpa']['users'] = DB::table('children')
                    ->whereIn('id', $stats['gpa']['children'])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        // 6. Tiêm chủng - vaccination_schedules
        if (in_array('vaccine', $allowedCodes)) {
            $vacAgg = DB::table('vaccination_schedules')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('COUNT(*) as total')
                ->first();
            $stats['vaccine']['count'] += (int) ($vacAgg->total ?? 0);

            $stats['vaccine']['children'] = DB::table('vaccination_schedules')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('child_id')
                ->distinct()
                ->pluck('child_id')
                ->toArray();

            if (!empty($stats['vaccine']['children'])) {
                $stats['vaccine']['users'] = DB::table('children')
                    ->whereIn('id', $stats['vaccine']['children'])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        // 7. Theo dõi thai kỳ - pregnancies
        if (in_array('pregnancy', $allowedCodes)) {
            $pregAgg = DB::table('pregnancies')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('COUNT(*) as total')
                ->first();
            $stats['pregnancy']['count'] += (int) ($pregAgg->total ?? 0);

            $stats['pregnancy']['children'] = DB::table('pregnancies')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('child_id')
                ->distinct()
                ->pluck('child_id')
                ->toArray();

            if (!empty($stats['pregnancy']['children'])) {
                $stats['pregnancy']['users'] = DB::table('children')
                    ->whereIn('id', $stats['pregnancy']['children'])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        // 8. Hồ sơ y tế - journals where type = 'prescription'
        if (in_array('diary_prescription', $allowedCodes)) {
            $presAgg = DB::table('journals')
                ->whereIn('type', ['prescription', 'precription', 1])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('COUNT(*) as total')
                ->first();
            $stats['diary_prescription']['count'] += (int) ($presAgg->total ?? 0);

            $stats['diary_prescription']['children'] = DB::table('journals')
                ->whereIn('type', ['prescription', 'precription', 1])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('child_id')
                ->distinct()
                ->pluck('child_id')
                ->toArray();

            if (!empty($stats['diary_prescription']['children'])) {
                $stats['diary_prescription']['users'] = DB::table('children')
                    ->whereIn('id', $stats['diary_prescription']['children'])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        // 9. Nhật ký khoảnh khắc - journals where type = 'moment'
        if (in_array('diary_moment', $allowedCodes)) {
            $momAgg = DB::table('journals')
                ->whereIn('type', ['moment', 0])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('COUNT(*) as total')
                ->first();
            $stats['diary_moment']['count'] += (int) ($momAgg->total ?? 0);

            $stats['diary_moment']['children'] = DB::table('journals')
                ->whereIn('type', ['moment', 0])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('child_id')
                ->distinct()
                ->pluck('child_id')
                ->toArray();

            if (!empty($stats['diary_moment']['children'])) {
                $stats['diary_moment']['users'] = DB::table('children')
                    ->whereIn('id', $stats['diary_moment']['children'])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        // 10. Tích hợp từ bảng feature_usages
        if (\Illuminate\Support\Facades\Schema::hasTable('feature_usages')) {
            $fuCounts = DB::table('feature_usages')
                ->whereIn('feature_code', $allowedCodes)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('feature_code, COUNT(*) as total')
                ->groupBy('feature_code')
                ->pluck('total', 'feature_code')
                ->toArray();

            foreach ($fuCounts as $code => $cnt) {
                if (isset($stats[$code])) {
                    if (in_array($code, ['predict_height', 'develop', 'store', 'clinic'])) {
                        $stats[$code]['count'] += (int) $cnt;
                    }
                }
            }

            $fuUsers = DB::table('feature_usages')
                ->whereIn('feature_code', $allowedCodes)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('user_id')
                ->select('feature_code', 'user_id')
                ->distinct()
                ->get();

            foreach ($fuUsers as $fu) {
                if (isset($stats[$fu->feature_code])) {
                    $stats[$fu->feature_code]['users'][] = $fu->user_id;
                }
            }

            $fuChildren = DB::table('feature_usages')
                ->whereIn('feature_code', $allowedCodes)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('child_id')
                ->select('feature_code', 'child_id')
                ->distinct()
                ->get();

            foreach ($fuChildren as $fc) {
                if (isset($stats[$fc->feature_code])) {
                    $stats[$fc->feature_code]['children'][] = $fc->child_id;
                }
            }
        }

        // Ensure unique arrays
        foreach ($stats as $code => &$val) {
            $val['users'] = array_values(array_unique($val['users']));
            $val['children'] = array_values(array_unique($val['children']));
        }
        unset($val);

        return $stats;
    }

    /**
     * Build time series data points for amCharts 5 using single SQL group-by queries.
     */
    protected function buildTrendTimeline(Carbon $startDate, Carbon $endDate, string $period, array $catalog): array
    {
        $series = [];
        foreach ($catalog as $code => $meta) {
            $series[] = [
                'field' => $code,
                'name' => $meta['short_name'],
                'color' => $meta['color'],
            ];
        }

        $days = $startDate->diffInDays($endDate);
        $isHourly = ($period === '1d' || $period === 'today' || $days <= 1);
        $format = $isHourly ? '%Y-%m-%d %H:00:00' : '%Y-%m-%d';

        // Pre-fetch all daily/hourly counts for all features with single group by queries
        $featureTimeCounts = [];

        // Ratings (IQ, EQ, AQ)
        $ratingCounts = DB::table('ratings')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("type, DATE_FORMAT(created_at, '{$format}') as t_point, COUNT(*) as cnt")
            ->groupBy('type', 't_point')
            ->get();
        foreach ($ratingCounts as $r) {
            $featureTimeCounts[$r->type][$r->t_point] = (int) $r->cnt;
        }

        // PQ
        $pqCounts = DB::table('ratings_pqs')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as t_point, COUNT(*) as cnt")
            ->groupBy('t_point')
            ->pluck('cnt', 't_point')
            ->toArray();
        $featureTimeCounts['pq'] = $pqCounts;

        // GPA
        $gpaCounts = DB::table('class_grades')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as t_point, COUNT(*) as cnt")
            ->groupBy('t_point')
            ->pluck('cnt', 't_point')
            ->toArray();
        $featureTimeCounts['gpa'] = $gpaCounts;

        // Vaccine
        $vacCounts = DB::table('vaccination_schedules')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as t_point, COUNT(*) as cnt")
            ->groupBy('t_point')
            ->pluck('cnt', 't_point')
            ->toArray();
        $featureTimeCounts['vaccine'] = $vacCounts;

        // Pregnancy
        $pregCounts = DB::table('pregnancies')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as t_point, COUNT(*) as cnt")
            ->groupBy('t_point')
            ->pluck('cnt', 't_point')
            ->toArray();
        $featureTimeCounts['pregnancy'] = $pregCounts;

        // Prescription & Moment
        $journalCounts = DB::table('journals')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("type, DATE_FORMAT(created_at, '{$format}') as t_point, COUNT(*) as cnt")
            ->groupBy('type', 't_point')
            ->get();
        foreach ($journalCounts as $j) {
            $code = in_array($j->type, ['prescription', 'precription', 1]) ? 'diary_prescription' : 'diary_moment';
            $featureTimeCounts[$code][$j->t_point] = ($featureTimeCounts[$code][$j->t_point] ?? 0) + (int) $j->cnt;
        }

        // Feature Usages
        if (\Illuminate\Support\Facades\Schema::hasTable('feature_usages')) {
            $fuCounts = DB::table('feature_usages')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw("feature_code, DATE_FORMAT(created_at, '{$format}') as t_point, COUNT(*) as cnt")
                ->groupBy('feature_code', 't_point')
                ->get();
            foreach ($fuCounts as $fu) {
                if (in_array($fu->feature_code, ['predict_height', 'develop', 'store', 'clinic'])) {
                    $featureTimeCounts[$fu->feature_code][$fu->t_point] = ($featureTimeCounts[$fu->feature_code][$fu->t_point] ?? 0) + (int) $fu->cnt;
                }
            }
        }

        $data = [];
        if ($isHourly) {
            for ($h = 0; $h < 24; $h++) {
                $pointDate = $startDate->copy()->addHours($h);
                $key = $pointDate->format('Y-m-d H:00:00');
                $label = sprintf('%02dh', $h);

                $row = ['date' => $label];
                foreach ($catalog as $code => $meta) {
                    $row[$code] = $featureTimeCounts[$code][$key] ?? 0;
                }
                $data[] = $row;
            }
        } elseif ($days <= 31) {
            for ($i = 0; $i <= $days; $i++) {
                $pointDate = $startDate->copy()->addDays($i);
                $key = $pointDate->format('Y-m-d');
                $label = $pointDate->format('d/m');

                $row = ['date' => $label];
                foreach ($catalog as $code => $meta) {
                    $row[$code] = $featureTimeCounts[$code][$key] ?? 0;
                }
                $data[] = $row;
            }
        } else {
            // Group by week
            $currentCursor = $startDate->copy()->startOfWeek();
            while ($currentCursor->lte($endDate)) {
                $weekEnd = $currentCursor->copy()->endOfWeek();
                $label = 'T' . $currentCursor->format('W') . ' (' . $currentCursor->format('d/m') . ')';

                $row = ['date' => $label];
                foreach ($catalog as $code => $meta) {
                    $sum = 0;
                    $dayCursor = $currentCursor->copy();
                    while ($dayCursor->lte($weekEnd) && $dayCursor->lte($endDate)) {
                        $k = $dayCursor->format('Y-m-d');
                        $sum += ($featureTimeCounts[$code][$k] ?? 0);
                        $dayCursor->addDay();
                    }
                    $row[$code] = $sum;
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
