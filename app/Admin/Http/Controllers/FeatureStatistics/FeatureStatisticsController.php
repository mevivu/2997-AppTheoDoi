<?php

namespace App\Admin\Http\Controllers\FeatureStatistics;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\FeatureStatisticsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FeatureStatisticsController extends Controller
{
    protected FeatureStatisticsService $statisticsService;

    public function __construct(FeatureStatisticsService $statisticsService)
    {
        parent::__construct();
        $this->statisticsService = $statisticsService;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.feature_statistics.index'
        ];
    }

    public function index(Request $request)
    {
        ini_set('memory_limit', '256M');
        $period = $request->get('period', '30d');
        $category = $request->get('category', 'evaluation');
        $from = $request->get('from');
        $to = $request->get('to');

        try {
            $stats = $this->statisticsService->getStatistics($period, $category, $from, $to);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $stats
                ]);
            }

            return view($this->view['index'], [
                'breadcrumbs' => $this->crums->add('Thống kê chức năng'),
                'stats' => $stats,
                'currentPeriod' => $period,
                'currentCategory' => $category,
                'from' => $from,
                'to' => $to,
            ]);
        } catch (\Exception $e) {
            \Log::error('FeatureStatisticsController error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Lỗi khi tải dữ liệu thống kê: ' . $e->getMessage());
        }
    }

    /**
     * Export feature usage report to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $period = $request->get('period', '30d');
        $category = $request->get('category', 'evaluation');
        $from = $request->get('from');
        $to = $request->get('to');

        $stats = $this->statisticsService->getStatistics($period, $category, $from, $to);
        $fileName = 'thong_ke_chuc_nang_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($stats) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility in Vietnamese
            fputs($file, "\xEF\xBB\xBF");

            // Header info
            fputcsv($file, ['BÁO CÁO THỐNG KÊ MỨC ĐỘ SỬ DỤNG CHỨC NĂNG']);
            fputcsv($file, ['Khoảng thời gian:', $stats['date_range_label']]);
            fputcsv($file, ['Tổng lượt sử dụng:', $stats['kpis']['total_usages']]);
            fputcsv($file, ['Số phụ huynh:', $stats['kpis']['unique_users_count']]);
            fputcsv($file, ['Số trẻ em:', $stats['kpis']['unique_children_count']]);
            fputcsv($file, []);

            // Table headers
            fputcsv($file, [
                'Xếp hạng',
                'Tên chức năng',
                'Nhóm chức năng',
                'Lượt sử dụng',
                'Tỷ trọng (%)',
                'Số phụ huynh',
                'Số trẻ em',
                'Tăng trưởng (%)',
            ]);

            // Rows
            foreach ($stats['ranking'] as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row['name'],
                    $row['category_name'],
                    $row['count'],
                    $row['percentage'] . '%',
                    $row['unique_users'],
                    $row['unique_children'],
                    ($row['growth'] >= 0 ? '+' : '') . $row['growth'] . '%',
                ]);
            }

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }
}
