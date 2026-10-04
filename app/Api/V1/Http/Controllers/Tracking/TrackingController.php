<?php

namespace App\Api\V1\Http\Controllers\Tracking;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\FeatureStatisticsService;
use App\Api\V1\Support\Response;
use App\Models\FeatureUsage;
use App\Services\Tracking\AppSessionTrackerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    use Response;

    protected AppSessionTrackerInterface $appSessionTracker;

    public function __construct(AppSessionTrackerInterface $appSessionTracker)
    {
        $this->appSessionTracker = $appSessionTracker;
    }

    /**
     * Log a feature usage event from the client app.
     */
    public function logFeatureUsage(Request $request): JsonResponse
    {
        $featureCode = $request->input('feature_code');
        if (empty($featureCode)) {
            return response()->json([
                'status' => 400,
                'message' => 'feature_code is required',
            ], 400);
        }

        $catalog = FeatureStatisticsService::getFeatureCatalog();
        $featureMeta = $catalog[$featureCode] ?? [
            'name' => ucfirst($featureCode),
            'category' => 'evaluation',
        ];

        $user = auth('api')->user();

        FeatureUsage::create([
            'user_id' => $user?->id,
            'child_id' => $request->input('child_id'),
            'feature_code' => $featureCode,
            'feature_name' => $featureMeta['name'],
            'category' => $featureMeta['category'],
            'action' => $request->input('action', 'view'),
            'metadata' => $request->input('metadata'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Logged successfully',
        ]);
    }

    /**
     * Ghi nhận tín hiệu hoạt động phiên ứng dụng (Heartbeat) của phụ huynh
     *
     * @authenticated
     * @bodyParam event string required Sự kiện: start (mở app), heartbeat (định kỳ mỗi 60s), end (thoát app). Example: heartbeat
     * @bodyParam seconds int Số giây hoạt động kể từ tín hiệu trước (mặc định 60). Example: 60
     */
    public function logAppSession(Request $request): JsonResponse
    {
        $request->validate([
            'event' => 'required|in:start,heartbeat,end',
            'seconds' => 'nullable|integer|min:0|max:120',
        ]);

        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => __('Vui lòng đăng nhập để ghi nhận hoạt động.'),
            ], 401);
        }

        $event = $request->input('event', 'heartbeat');
        $seconds = (int) $request->input('seconds', 60);

        $activity = $this->appSessionTracker->recordHeartbeat($user, $event, $seconds);

        return response()->json([
            'status' => 200,
            'message' => 'Heartbeat recorded successfully',
            'data' => [
                'activity_date' => $activity->activity_date->toDateString(),
                'active_seconds' => $activity->active_seconds,
                'session_count' => $activity->session_count,
            ],
        ]);
    }
}
