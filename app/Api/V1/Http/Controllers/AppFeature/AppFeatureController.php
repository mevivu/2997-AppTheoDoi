<?php

namespace App\Api\V1\Http\Controllers\AppFeature;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Models\AppFeature;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Cài đặt
 */
class AppFeatureController extends Controller
{
    use Response, UseLog;

    /**
     * Danh sách bật/tắt chức năng theo nền tảng
     *
     * API public (khách chưa đăng nhập cũng gọi được). Chỉ trả các chức năng ĐANG TẮT
     * trên nền tảng yêu cầu; chức năng không có trong danh sách = đang bật.
     *
     * @queryParam platform string Nền tảng: ios | android. Default: android. Example: ios
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": {
     *         "version": 1760505600,
     *         "features": [
     *             {"code": "referral", "enabled": false, "mode": "hide", "message": null}
     *         ]
     *     }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $platform = AppFeature::normalizePlatform($request->query('platform', $request->header('X-Platform')));
            $all = AppFeature::allCached();

            $features = $all
                ->reject(fn (AppFeature $f) => $f->isEnabledFor($platform))
                ->map(fn (AppFeature $f) => [
                    'code' => $f->code,
                    'enabled' => false,
                    'mode' => $f->disabled_mode,
                    'message' => $f->disabled_message,
                ])
                ->values();

            return $this->jsonResponseSuccess([
                'version' => (int) optional($all->max('updated_at'))->timestamp,
                'features' => $features,
            ]);
        } catch (Exception $e) {
            $this->logError('Get app features failed:', $e);
            return $this->jsonResponseError('Get app features failed', 500);
        }
    }
}
