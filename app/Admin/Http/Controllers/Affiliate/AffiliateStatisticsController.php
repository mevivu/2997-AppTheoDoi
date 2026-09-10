<?php

namespace App\Admin\Http\Controllers\Affiliate;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\Affiliate\AffiliateStatisticsService;
use App\Traits\RouteAdminSystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AffiliateStatisticsController extends Controller
{
    protected AffiliateStatisticsService $statisticsService;

    public function __construct(AffiliateStatisticsService $statisticsService)
    {
        parent::__construct();
        $this->statisticsService = $statisticsService;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.affiliate_statistics.index'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => RouteAdminSystem::AFFILIATE_STATISTICS_INDEX
        ];
    }

    /**
     * Màn hình chính thống kê doanh thu đối tác & Xếp hạng
     */
    public function index(Request $request)
    {
        ini_set('memory_limit', '256M');

        $period = $request->get('period', '30d');
        $rank = $request->get('rank', 'all');
        $from = $request->get('from');
        $to = $request->get('to');
        $search = $request->get('search');

        try {
            $stats = $this->statisticsService->getStatistics($period, $rank, $from, $to, $search);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $stats
                ]);
            }

            return view($this->view['index'], [
                'breadcrumbs' => $this->crums->add(__('Thống kê Đối tác')),
                'stats' => $stats,
                'currentPeriod' => $period,
                'currentRank' => $rank,
                'from' => $from,
                'to' => $to,
                'search' => $search,
            ]);
        } catch (\Throwable $e) {
            Log::error('AffiliateStatisticsController error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lỗi khi tải dữ liệu thống kê: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Lỗi khi tải dữ liệu thống kê: ' . $e->getMessage());
        }
    }

    /**
     * Lấy chi tiết danh sách F1 và các đơn mua gói của 1 đối tác (Ajax Pop-up)
     */
    public function partnerDetails(Request $request, $id): JsonResponse
    {
        $period = $request->get('period', '30d');
        $from = $request->get('from');
        $to = $request->get('to');

        try {
            $details = $this->statisticsService->getPartnerF1Details((int)$id, $period, $from, $to);

            return response()->json([
                'status' => 'success',
                'data' => $details
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi lấy chi tiết F1 đối tác: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể tải chi tiết đối tác: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kích hoạt đồng bộ cấp bậc & doanh số realtime
     */
    public function syncRanks(): JsonResponse
    {
        try {
            Artisan::call('affiliate:sync-ranks');
            $output = Artisan::output();

            return response()->json([
                'status' => 'success',
                'message' => 'Đồng bộ cấp bậc và doanh số đối tác thành công!',
                'log' => $output
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi chạy artisan affiliate:sync-ranks: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi đồng bộ: ' . $e->getMessage()
            ], 500);
        }
    }
}
