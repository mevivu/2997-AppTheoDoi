<?php

namespace App\Api\V1\Http\Controllers\Bank;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Repositories\Bank\BankRepositoryInterface;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @group Danh mục ngân hàng
 */
class BankController extends Controller
{
    use Response, UseLog;

    protected BankRepositoryInterface $bankRepository;

    public function __construct(BankRepositoryInterface $bankRepository)
    {
        $this->bankRepository = $bankRepository;
    }

    /**
     * Lấy danh sách ngân hàng Việt Nam (hỗ trợ tìm kiếm theo tên, mã hoặc viết tắt)
     *
     * @queryParam keyword string Từ khóa tìm kiếm (tên, mã, tên viết tắt). Example: Vietcombank
     *
     * @response 200 {
     *   "status": 200,
     *   "message": "Thực hiện thành công.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "bin": "970415",
     *       "shortName": "VietinBank",
     *       "logo": "https://cdn.vietqr.io/img/ICB.png",
     *       "transfer_supported": 1,
     *       "lookup_supported": 1,
     *       "name": "Ngân hàng TMCP Công thương Việt Nam",
     *       "code": "ICB"
     *     }
     *   ]
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $keyword = $request->input('keyword');
            $banks = $this->bankRepository->getAllBanks($keyword);

            return $this->jsonResponseSuccess($banks);
        } catch (Throwable $e) {
            $this->logError('Get banks failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }
}
