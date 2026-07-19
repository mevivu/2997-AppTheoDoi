<?php

namespace App\Api\V1\Http\Controllers\AppVersion;

use App\Http\Controllers\Controller;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Services\AppVersion\AppVersionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AppVersionController extends Controller
{
    use Response, UseLog;

    protected AppVersionServiceInterface $appVersionService;

    public function __construct(AppVersionServiceInterface $appVersionService)
    {
        $this->appVersionService = $appVersionService;
    }

    /**
     * Kiểm tra phiên bản ứng dụng
     */
    public function check(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'app_type' => 'nullable|string',
                'platform' => 'required|string|in:android,ios',
                'check' => 'nullable|string',
                'current_version' => 'nullable|string',
            ]);

            $appType = $request->input('app_type', 'user');
            $versionToCheck = $request->input('check') ?? $request->input('current_version');

            $result = $this->appVersionService->checkVersion(
                $appType,
                $request->input('platform'),
                $versionToCheck
            );

            return $this->jsonResponseSuccess($result, __('Kiểm tra phiên bản thành công.'));

        } catch (Exception $e) {
            $this->logError('Lỗi kiểm tra phiên bản: ', $e);
            return $this->jsonResponseError($e->getMessage(), 400);
        }
    }
}
