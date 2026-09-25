<?php

namespace App\Api\V1\Http\Controllers\Memo;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Services\Memo\MemoPersonalBestService;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Memo Personal Best API
 */
class MemoPersonalBestController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected MemoPersonalBestService $personalBestService;

    public function __construct(MemoPersonalBestService $personalBestService)
    {
        $this->middleware('auth:api');
        $this->personalBestService = $personalBestService;
    }

    /**
     * Lấy danh sách thành tích cá nhân của bé theo từng level và level cao nhất
     *
     * @param int $childId
     * @return JsonResponse
     */
    public function show(int $childId): JsonResponse
    {
        try {
            $data = $this->personalBestService->getChildPersonalBests($childId);

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $this->logError('Lỗi lấy thành tích cá nhân Memo Game', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi tải thành tích.', 500);
        }
    }
}
