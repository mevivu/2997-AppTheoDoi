<?php

namespace App\Admin\Services\Affiliate;

use App\Enums\Transaction\TransactionStatus;
use App\Enums\User\AffiliateRank;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AffiliateStatisticsService
{
    /**
     * Lấy toàn bộ dữ liệu thống kê đối tác: KPI, Trend Chart, Donut Chart, Top 10 Bar Chart, Bảng xếp hạng
     *
     * @param string $period '1d'|'7d'|'30d'|'this_month'|'last_month'|'all'|'custom'
     * @param string $rank 'all'|'1'|'2'|'3'|'4'
     * @param string|null $from dd/mm/yyyy hoặc Y-m-d
     * @param string|null $to dd/mm/yyyy hoặc Y-m-d
     * @param string|null $search Tìm kiếm tên, sđt hoặc mã
     * @return array
     */
    public function getStatistics(string $period = '30d', string $rank = 'all', ?string $from = null, ?string $to = null, ?string $search = null): array
    {
        // 1. Xác định mốc thời gian truy vấn
        [$startDate, $endDate, $dateRangeLabel, $groupBy] = $this->resolveDateRange($period, $from, $to);
        [$prevStartDate, $prevEndDate] = $this->resolvePreviousDateRange($startDate, $endDate);

        // 2. Thống kê Doanh số F1 theo từng Đối tác trong kỳ
        $partnerSalesQuery = DB::table('transactions as t')
            ->join('users as u', 't.user_id', '=', 'u.id')
            ->whereNotNull('u.referrer_id')
            ->where('t.status', TransactionStatus::Confirmed->value);

        if ($startDate && $endDate) {
            $partnerSalesQuery->whereBetween('t.created_at', [$startDate, $endDate]);
        }

        $periodPartnerSales = $partnerSalesQuery
            ->select(
                'u.referrer_id as partner_id',
                DB::raw('SUM(t.amount) as period_sales'),
                DB::raw('COUNT(t.id) as period_orders'),
                DB::raw('COUNT(DISTINCT t.user_id) as period_paying_f1s')
            )
            ->groupBy('u.referrer_id')
            ->get()
            ->keyBy('partner_id');

        // 3. Thống kê Doanh số F1 kỳ trước để tính tăng trưởng KPI
        $prevTotalRevenue = 0;
        if ($prevStartDate && $prevEndDate) {
            $prevTotalRevenue = (float) DB::table('transactions as t')
                ->join('users as u', 't.user_id', '=', 'u.id')
                ->whereNotNull('u.referrer_id')
                ->where('t.status', TransactionStatus::Confirmed->value)
                ->whereBetween('t.created_at', [$prevStartDate, $prevEndDate])
                ->sum('t.amount');
        }

        // 4. Lấy danh sách đối tác
        $usersQuery = User::query();

        if (!empty($search)) {
            $cleanSearch = trim($search);
            $usersQuery->where(function ($q) use ($cleanSearch) {
                $q->where('fullname', 'like', "%{$cleanSearch}%")
                  ->orWhere('phone', 'like', "%{$cleanSearch}%")
                  ->orWhere('email', 'like', "%{$cleanSearch}%")
                  ->orWhere('affiliate_code', 'like', "%{$cleanSearch}%");
            });
        } else {
            // Mặc định lấy các đối tác có phát sinh hoạt động: Có doanh số, có F1 hoặc có số dư ví
            $usersQuery->where(function ($q) {
                $q->where('affiliate_total_sales', '>', 0)
                  ->orWhere('wallet_balance', '>', 0)
                  ->orWhereExists(function ($sub) {
                      $sub->select(DB::raw(1))
                          ->from('users as f1')
                          ->whereColumn('f1.referrer_id', 'users.id');
                  });
            });
        }

        // Lọc theo cấp bậc nếu có
        if ($rank !== 'all' && in_array((int)$rank, [1, 2, 3, 4])) {
            $usersQuery->where('affiliate_rank', (int)$rank);
        }

        $allPartners = $usersQuery->limit(200)->get();

        // Đếm số lượng F1 của từng đối tác (Tổng & trong kỳ)
        $f1StatsQuery = DB::table('users')
            ->whereNotNull('referrer_id');
        
        $totalF1ByPartner = (clone $f1StatsQuery)
            ->select('referrer_id', DB::raw('COUNT(*) as total_count'))
            ->groupBy('referrer_id')
            ->pluck('total_count', 'referrer_id');

        $periodF1ByPartner = collect();
        if ($startDate && $endDate) {
            $periodF1ByPartner = (clone $f1StatsQuery)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('referrer_id', DB::raw('COUNT(*) as period_count'))
                ->groupBy('referrer_id')
                ->pluck('period_count', 'referrer_id');
        }

        // 5. Kết hợp dữ liệu và tạo Bảng Xếp Hạng (Ranking List)
        $ranking = [];
        $totalRevenue = 0;
        $totalOrders = 0;
        $activePartnersCount = 0;

        foreach ($allPartners as $partner) {
            $stat = $periodPartnerSales->get($partner->id);
            $periodSales = $stat ? (float) $stat->period_sales : 0.0;
            $periodOrders = $stat ? (int) $stat->period_orders : 0;
            $totalF1 = $totalF1ByPartner->get($partner->id, 0);
            $periodNewF1 = $periodF1ByPartner->get($partner->id, 0);

            if ($periodSales > 0) {
                $totalRevenue += $periodSales;
                $totalOrders += $periodOrders;
                $activePartnersCount++;
            }

            // Lấy rank enum an toàn
            $rankEnum = $partner->affiliate_rank instanceof AffiliateRank
                ? $partner->affiliate_rank
                : AffiliateRank::tryFrom((int)$partner->affiliate_rank) ?? AffiliateRank::Bronze;

            $ranking[] = [
                'id' => $partner->id,
                'fullname' => $partner->fullname ?: 'Khách hàng #' . $partner->id,
                'phone' => $partner->phone ?: 'Chưa cập nhật',
                'email' => $partner->email ?: '',
                'avatar' => $partner->avatar,
                'affiliate_code' => $partner->affiliate_code ?: ('CC' . str_pad($partner->id, 5, '0', STR_PAD_LEFT)),
                'rank_id' => $rankEnum->value,
                'rank_name' => $rankEnum->name(),
                'rank_badge' => $rankEnum->badge(),
                'rank_icon' => $rankEnum->icon(),
                'rank_color' => $rankEnum->colorHex(),
                'period_sales' => $periodSales,
                'period_orders' => $periodOrders,
                'total_sales' => (float) ($partner->affiliate_total_sales ?? 0),
                'wallet_balance' => (float) ($partner->wallet_balance ?? 0),
                'total_f1' => $totalF1,
                'period_new_f1' => $periodNewF1,
                'created_at' => $partner->created_at ? $partner->created_at->format('d/m/Y') : '',
            ];
        }

        // Sắp xếp bảng xếp hạng: Ưu tiên Doanh thu trong kỳ DESC, sau đó Doanh số tích lũy DESC
        usort($ranking, function ($a, $b) {
            if ($b['period_sales'] != $a['period_sales']) {
                return $b['period_sales'] <=> $a['period_sales'];
            }
            return $b['total_sales'] <=> $a['total_sales'];
        });

        // 6. Tính toán KPI Metrics
        $aov = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;
        $growth = 0.0;
        if ($prevTotalRevenue > 0) {
            $growth = round((($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100, 1);
        } elseif ($totalRevenue > 0) {
            $growth = 100.0;
        }

        $kpis = [
            'total_revenue' => $totalRevenue,
            'total_revenue_formatted' => number_format($totalRevenue, 0, ',', '.') . 'đ',
            'active_partners' => $activePartnersCount,
            'total_orders' => $totalOrders,
            'aov' => $aov,
            'aov_formatted' => number_format($aov, 0, ',', '.') . 'đ',
            'growth' => $growth,
            'prev_revenue' => $prevTotalRevenue,
        ];

        // 7. Chuẩn bị Dữ liệu Biểu đồ Xu hướng (Trend Chart)
        $trendData = $this->buildTrendChartData($startDate, $endDate, $groupBy);

        // 8. Chuẩn bị Dữ liệu Biểu đồ Donut (Tỷ trọng Cấp bậc)
        $donutData = $this->buildDonutChartData($ranking, $totalRevenue);

        // 9. Chuẩn bị Dữ liệu Biểu đồ Top 10 (Bar Chart)
        $top10BarData = $this->buildTop10BarChartData($ranking);

        return [
            'date_range_label' => $dateRangeLabel,
            'kpis' => $kpis,
            'trend_data' => $trendData,
            'donut_data' => $donutData,
            'top10_data' => $top10BarData,
            'ranking' => $ranking,
            'total_partners' => count($ranking),
        ];
    }

    /**
     * Lấy chi tiết đơn hàng F1 của một đối tác cụ thể (dành cho Ajax Modal)
     *
     * @param int $partnerId
     * @param string $period
     * @param string|null $from
     * @param string|null $to
     * @return array
     */
    public function getPartnerF1Details(int $partnerId, string $period = '30d', ?string $from = null, ?string $to = null): array
    {
        $partner = User::findOrFail($partnerId);
        [$startDate, $endDate] = $this->resolveDateRange($period, $from, $to);

        $rankEnum = $partner->affiliate_rank instanceof AffiliateRank
            ? $partner->affiliate_rank
            : AffiliateRank::tryFrom((int)$partner->affiliate_rank) ?? AffiliateRank::Bronze;

        // Danh sách toàn bộ F1
        $f1Users = User::where('referrer_id', $partnerId)
            ->select('id', 'fullname', 'phone', 'email', 'avatar', 'created_at')
            ->get()
            ->keyBy('id');

        // Giao dịch mua gói của F1
        $transactionsQuery = Transaction::query()
            ->whereIn('user_id', $f1Users->keys())
            ->where('status', TransactionStatus::Confirmed->value)
            ->with(['user:id,fullname,phone,email,avatar']);

        if ($startDate && $endDate) {
            $transactionsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $transactions = $transactionsQuery->orderBy('created_at', 'desc')->get();

        // Lấy tên gói dịch vụ
        $packageIds = $transactions->pluck('package_id')->unique()->filter();
        $packages = Package::whereIn('id', $packageIds)->pluck('name', 'id');

        $orderList = [];
        $periodTotalSales = 0;

        foreach ($transactions as $tx) {
            $periodTotalSales += (float) $tx->amount;
            $f1 = $tx->user;
            $orderList[] = [
                'id' => $tx->id,
                'code' => $tx->code,
                'order_id' => $tx->google_order_id ?: $tx->code,
                'f1_name' => $f1 ? ($f1->fullname ?: 'Khách hàng #' . $f1->id) : 'Không rõ',
                'f1_phone' => $f1 ? ($f1->phone ?: '-') : '-',
                'package_name' => $packages->get($tx->package_id, 'Gói dịch vụ VIP'),
                'amount' => (float) $tx->amount,
                'amount_formatted' => number_format($tx->amount, 0, ',', '.') . 'đ',
                'service' => $tx->service?->value ?? 'Google/Apple',
                'created_at' => $tx->created_at ? $tx->created_at->format('d/m/Y H:i') : '',
            ];
        }

        return [
            'partner' => [
                'id' => $partner->id,
                'fullname' => $partner->fullname ?: 'Khách hàng #' . $partner->id,
                'phone' => $partner->phone ?: '-',
                'email' => $partner->email ?: '-',
                'affiliate_code' => $partner->affiliate_code,
                'rank_name' => $rankEnum->name(),
                'rank_badge' => $rankEnum->badge(),
                'rank_color' => $rankEnum->colorHex(),
                'total_sales' => (float) ($partner->affiliate_total_sales ?? 0),
                'total_sales_formatted' => number_format($partner->affiliate_total_sales ?? 0, 0, ',', '.') . 'đ',
                'wallet_balance' => (float) ($partner->wallet_balance ?? 0),
                'wallet_balance_formatted' => number_format($partner->wallet_balance ?? 0, 0, ',', '.') . 'đ',
                'total_f1_count' => $f1Users->count(),
            ],
            'period_sales' => $periodTotalSales,
            'period_sales_formatted' => number_format($periodTotalSales, 0, ',', '.') . 'đ',
            'orders' => $orderList,
            'total_orders' => count($orderList),
        ];
    }

    /**
     * Xây dựng dữ liệu biểu đồ xu hướng doanh thu theo thời gian (amCharts 5 XY Line/Column)
     */
    protected function buildTrendChartData(?Carbon $startDate, ?Carbon $endDate, string $groupBy): array
    {
        if (!$startDate || !$endDate) {
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
        }

        // Truy vấn doanh thu theo mốc thời gian
        $formatSql = match ($groupBy) {
            'hour' => "DATE_FORMAT(t.created_at, '%H:00')",
            'month' => "DATE_FORMAT(t.created_at, '%m/%Y')",
            default => "DATE_FORMAT(t.created_at, '%Y-%m-%d')",
        };

        $rawStats = DB::table('transactions as t')
            ->join('users as u', 't.user_id', '=', 'u.id')
            ->whereNotNull('u.referrer_id')
            ->where('t.status', TransactionStatus::Confirmed->value)
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->select(
                DB::raw("{$formatSql} as time_key"),
                DB::raw('SUM(t.amount) as total_amount')
            )
            ->groupBy('time_key')
            ->pluck('total_amount', 'time_key');

        $rawOrders = DB::table('transactions as t')
            ->join('users as u', 't.user_id', '=', 'u.id')
            ->whereNotNull('u.referrer_id')
            ->where('t.status', TransactionStatus::Confirmed->value)
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->select(
                DB::raw("{$formatSql} as time_key"),
                DB::raw('COUNT(t.id) as total_orders')
            )
            ->groupBy('time_key')
            ->pluck('total_orders', 'time_key');

        $chartData = [];

        if ($groupBy === 'hour') {
            for ($h = 0; $h < 24; $h++) {
                $key = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
                $chartData[] = [
                    'date' => $key,
                    'sales' => (float) ($rawStats->get($key, 0)),
                    'orders' => (int) ($rawOrders->get($key, 0)),
                ];
            }
        } elseif ($groupBy === 'month') {
            $current = $startDate->copy()->startOfMonth();
            while ($current->lte($endDate)) {
                $key = $current->format('m/Y');
                $chartData[] = [
                    'date' => $key,
                    'sales' => (float) ($rawStats->get($key, 0)),
                    'orders' => (int) ($rawOrders->get($key, 0)),
                ];
                $current->addMonth();
            }
        } else {
            // Theo ngày (daily)
            $period = CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                $rawKey = $date->format('Y-m-d');
                $displayKey = $date->format('d/m');
                $chartData[] = [
                    'date' => $displayKey,
                    'full_date' => $date->format('d/m/Y'),
                    'sales' => (float) ($rawStats->get($rawKey, 0)),
                    'orders' => (int) ($rawOrders->get($rawKey, 0)),
                ];
            }
        }

        return $chartData;
    }

    /**
     * Xây dựng dữ liệu biểu đồ tỷ trọng Cấp bậc (amCharts 5 Donut Chart)
     */
    protected function buildDonutChartData(array $ranking, float $totalRevenue): array
    {
        $rankGroups = [
            1 => ['category' => 'Mẹ Đồng', 'value' => 0.0, 'color' => '#CD7F32', 'count' => 0],
            2 => ['category' => 'Mẹ Bạc', 'value' => 0.0, 'color' => '#6C757D', 'count' => 0],
            3 => ['category' => 'Mẹ Vàng', 'value' => 0.0, 'color' => '#E5A100', 'count' => 0],
            4 => ['category' => 'Mẹ Kim Cương', 'value' => 0.0, 'color' => '#00B4D8', 'count' => 0],
        ];

        foreach ($ranking as $item) {
            $rId = (int)$item['rank_id'];
            if (isset($rankGroups[$rId])) {
                $rankGroups[$rId]['value'] += $item['period_sales'];
                $rankGroups[$rId]['count']++;
            }
        }

        $donut = [];
        foreach ($rankGroups as $rId => $data) {
            $donut[] = [
                'category' => $data['category'],
                'value' => (float) $data['value'],
                'color' => $data['color'],
                'count' => $data['count'],
                'percentage' => $totalRevenue > 0 ? round(($data['value'] / $totalRevenue) * 100, 1) : 0,
            ];
        }

        return $donut;
    }

    /**
     * Xây dựng dữ liệu biểu đồ Top 10 Đối tác Doanh thu Khủng (amCharts 5 Bar Chart)
     */
    protected function buildTop10BarChartData(array $ranking): array
    {
        $top10 = array_slice($ranking, 0, 10);
        $barData = [];

        // Đảo ngược thứ tự để trên biểu đồ ngang người cao nhất nằm ở trên cùng
        $reversed = array_reverse($top10);

        foreach ($reversed as $item) {
            $shortName = $item['fullname'];
            if (mb_strlen($shortName) > 16) {
                $shortName = mb_substr($shortName, 0, 15) . '...';
            }
            $label = $shortName . ' (' . $item['affiliate_code'] . ')';

            $barData[] = [
                'partner' => $label,
                'full_name' => $item['fullname'],
                'code' => $item['affiliate_code'],
                'sales' => (float) $item['period_sales'],
                'orders' => (int) $item['period_orders'],
                'rank' => $item['rank_name'],
                'color' => $item['rank_color'],
            ];
        }

        return $barData;
    }

    /**
     * Xác định ngày bắt đầu và kết thúc theo period hoặc custom date range
     */
    protected function resolveDateRange(string $period, ?string $from, ?string $to): array
    {
        $now = now();

        switch ($period) {
            case '1d':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                return [$start, $end, 'Hôm nay (' . $start->format('d/m/Y') . ')', 'hour'];

            case '7d':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                return [$start, $end, $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'), 'day'];

            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                return [$start, $end, 'Tháng ' . $start->format('m/Y'), 'day'];

            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                return [$start, $end, 'Tháng ' . $start->format('m/Y'), 'day'];

            case 'all':
                $start = Carbon::parse('2023-01-01')->startOfDay();
                $end = $now->copy()->endOfDay();
                return [$start, $end, 'Toàn bộ thời gian', 'month'];

            case 'custom':
                if (!empty($from) && !empty($to)) {
                    try {
                        $start = str_contains($from, '/')
                            ? Carbon::createFromFormat('d/m/Y', trim($from))->startOfDay()
                            : Carbon::parse($from)->startOfDay();

                        $end = str_contains($to, '/')
                            ? Carbon::createFromFormat('d/m/Y', trim($to))->endOfDay()
                            : Carbon::parse($to)->endOfDay();

                        $days = $start->diffInDays($end);
                        $groupBy = $days > 60 ? 'month' : 'day';

                        return [$start, $end, $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'), $groupBy];
                    } catch (\Throwable $e) {
                        // Fallback về 30 ngày
                    }
                }
                // Fallthrough if custom parsing fails

            case '30d':
            default:
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                return [$start, $end, $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'), 'day'];
        }
    }

    /**
     * Xác định khoảng ngày kỳ trước tương đương để tính toán tăng trưởng
     */
    protected function resolvePreviousDateRange(?Carbon $startDate, ?Carbon $endDate): array
    {
        if (!$startDate || !$endDate) {
            return [null, null];
        }

        $diffDays = $startDate->diffInDays($endDate) + 1;
        $prevEnd = $startDate->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($diffDays - 1)->startOfDay();

        return [$prevStart, $prevEnd];
    }
}
