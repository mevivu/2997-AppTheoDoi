<?php

namespace App\Admin\Http\Controllers\DeviceStatistics;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\Device\DeviceStatisticsService;
use Illuminate\Http\Request;

class DeviceStatisticsController extends Controller
{
    protected DeviceStatisticsService $deviceService;

    public function __construct(
        DeviceStatisticsService $deviceService
    ) {
        parent::__construct();
        $this->deviceService = $deviceService;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.device_statistics.index'
        ];
    }

    public function index(Request $request)
    {
        $deviceStats = $this->deviceService->getPlatformStats();

        if ($request->ajax()) {
            return response()->json([
                'status' => 200,
                'data' => $deviceStats,
            ]);
        }

        return view($this->view['index'], [
            'breadcrumbs' => $this->crums->add('Thống kê thiết bị'),
            'deviceStats' => $deviceStats,
        ]);
    }
}
