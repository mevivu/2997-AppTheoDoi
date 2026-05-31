<?php

namespace App\Admin\Http\Controllers\FirebaseReport;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\FirebaseAnalyticsService;

class FirebaseReportController extends Controller
{
    protected FirebaseAnalyticsService $analyticsService;

    public function __construct(
        FirebaseAnalyticsService $analyticsService
    ) {
        parent::__construct();
        $this->analyticsService = $analyticsService;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.firebase_report.index'
        ];
    }

    public function index()
    {
        if (request()->ajax()) {
            $period = request()->get('period', '30d');
            try {
                return response()->json([
                    'activeUsers' => $this->analyticsService->getActiveUsersOverTime($period)
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 400);
            }
        }

        $activeUsersOverTime = [];
        $analyticsError = null;
        try {
            $activeUsersOverTime = $this->analyticsService->getActiveUsersOverTime('30d');
        } catch (\Exception $e) {
            $analyticsError = $e->getMessage();
        }

        return view($this->view['index'], [
            'breadcrumbs' => $this->crums->add('Thống kê Firebase'),
            'activeUsersOverTime' => $activeUsersOverTime,
            'analyticsError' => $analyticsError,
        ]);
    }
}
