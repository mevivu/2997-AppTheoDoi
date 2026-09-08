<?php

namespace App\Api\V1\Http\Controllers\Tracking;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\FeatureStatisticsService;
use App\Api\V1\Support\Response;
use App\Models\FeatureUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    use Response;

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
}
