<?php

namespace App\Api\V1\Http\Controllers\Memo;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Memo\MemoCompetitionCompleteRequest;
use App\Api\V1\Http\Requests\Memo\MemoCompetitionStartRequest;
use App\Api\V1\Http\Requests\Memo\MemoCompetitionSubmitRoundRequest;
use App\Api\V1\Services\Memo\MemoCompetitionService;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Memo Competition API
 */
class MemoCompetitionApiController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected MemoCompetitionService $competitionService;

    public function __construct(MemoCompetitionService $competitionService)
    {
        $this->middleware('auth:api')->except(['index', 'featured', 'show', 'leaderboard']);
        $this->competitionService = $competitionService;
    }

    /**
     * Giải đấu nổi bật hiển thị ở trang chủ ứng dụng (Banner Home)
     * GET /api/v1/memo-competitions/featured
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $childId = $request->query('child_id') ? (int) $request->query('child_id') : null;
            $data = $this->competitionService->getFeatured($childId);

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $this->logError('Lỗi lấy giải đấu nổi bật Memo Game', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy thông tin giải đấu.', 500);
        }
    }

    /**
     * Danh sách các giải đấu (đang diễn ra, sắp diễn ra, đã kết thúc)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $childId = $request->query('child_id') ? (int) $request->query('child_id') : null;
            $data = $this->competitionService->getActiveAndUpcoming($childId);

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $this->logError('Lỗi lấy danh sách giải đấu Memo Game', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh sách giải đấu.', 500);
        }
    }

    /**
     * Chi tiết một giải đấu
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $childId = $request->query('child_id') ? (int) $request->query('child_id') : null;
            $data = $this->competitionService->getDetail($id, $childId);

            if (!$data) {
                return $this->jsonResponseError('Không tìm thấy giải đấu.', 404);
            }

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $this->logError('Lỗi lấy chi tiết giải đấu Memo Game', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy thông tin giải đấu.', 500);
        }
    }

    /**
     * Bắt đầu một lượt thi (Entry) trong giải đấu
     * Trả về dữ liệu 4 ván game không cho xem trước (peek_time = 0)
     */
    public function start(int $id, MemoCompetitionStartRequest $request): JsonResponse
    {
        try {
            $childId = (int) $request->validated()['child_id'];
            $data = $this->competitionService->startEntry($id, $childId);

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $this->logError('Lỗi bắt đầu lượt thi giải đấu Memo Game', $e);
            return $this->jsonResponseError($e->getMessage() ?: 'Lỗi hệ thống khi bắt đầu lượt thi.', $code);
        }
    }

    /**
     * Submit kết quả của 1 ván game trong lượt thi
     */
    public function submitRound(int $id, MemoCompetitionSubmitRoundRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $childId = (int) $validated['child_id'];
            $data = $this->competitionService->submitRound($id, $childId, $validated);

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $this->logError('Lỗi lưu kết quả ván thi giải đấu', $e);
            return $this->jsonResponseError($e->getMessage() ?: 'Lỗi khi lưu kết quả ván thi.', $code);
        }
    }

    /**
     * Hoàn thành lượt thi giải đấu
     */
    public function complete(int $id, MemoCompetitionCompleteRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $childId = (int) $validated['child_id'];
            $entryId = (int) $validated['entry_id'];

            $data = $this->competitionService->completeEntry($id, $childId, $entryId);

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $this->logError('Lỗi hoàn thành lượt thi giải đấu', $e);
            return $this->jsonResponseError($e->getMessage() ?: 'Lỗi hệ thống khi hoàn thành lượt thi.', $code);
        }
    }

    /**
     * Bảng xếp hạng của giải đấu
     */
    public function leaderboard(int $id, Request $request): JsonResponse
    {
        try {
            $childId = $request->query('child_id') ? (int) $request->query('child_id') : null;
            $limit = $request->query('limit') ? min(100, (int) $request->query('limit')) : 50;

            $data = $this->competitionService->getLeaderboard($id, $childId, $limit);

            return $this->jsonResponseSuccess($data);
        } catch (Exception $e) {
            $this->logError('Lỗi lấy bảng xếp hạng giải đấu Memo Game', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy bảng xếp hạng.', 500);
        }
    }
}
