<?php

namespace App\Api\V1\Http\Controllers\Transaction;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Transaction\WithdrawRequest;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Services\Withdraw\WithdrawService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @group Giao dịch rút tiền hoa hồng
 */
class WithdrawController extends Controller
{
    use Response, UseLog;

    protected WithdrawService $withdrawService;

    public function __construct(WithdrawService $withdrawService)
    {
        $this->withdrawService = $withdrawService;
        $this->middleware('auth:api');
    }

    /**
     * Lấy cấu hình rút tiền, quy định chi trả và thông tin ngân hàng đã lưu gần nhất
     *
     * @authenticated
     * @response 200 {
     *   "status": 200,
     *   "message": "Thực hiện thành công.",
     *   "data": {
     *       "wallet_balance": 3500000,
     *       "wallet_balance_formatted": "3.500.000đ",
     *       "min_balance": 1000000,
     *       "min_balance_formatted": "1.000.000đ",
     *       "step_multiple": 1000000,
     *       "step_multiple_formatted": "1.000.000đ",
     *       "is_eligible": true,
     *       "max_withdrawable": 3000000,
     *       "max_withdrawable_formatted": "3.000.000đ",
     *       "payout_day_text": "Thứ 5 hàng tuần",
     *       "payout_note": "Hệ thống tiếp nhận yêu cầu rút tiền 24/7. Các yêu cầu hợp lệ sẽ được tổng hợp, đối soát và chuyển khoản vào Thứ 5 hàng tuần.",
     *       "next_payout_date": "2026-09-17",
     *       "next_payout_text": "Thứ 5 (17/09/2026)",
     *       "saved_bank": {
     *           "bank_name": "Vietcombank",
     *           "bank_account_number": "0123456789",
     *           "bank_account_name": "NGUYEN VAN A"
     *       }
     *   }
     * }
     */
    public function getConfig(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $config = $this->withdrawService->getWithdrawConfigForUser($user);

            return $this->jsonResponseSuccess($config);
        } catch (Throwable $e) {
            $this->logError('Get withdraw config failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    /**
     * Gửi yêu cầu rút tiền hoa hồng
     *
     * @authenticated
     * @bodyParam amount numeric required Số tiền cần rút (>= 1.000.000đ, bội số của 1.000.000đ). Example: 2000000
     * @bodyParam bank_name string required Tên ngân hàng nhận tiền. Example: Vietcombank
     * @bodyParam bank_account_number string required Số tài khoản nhận tiền. Example: 0123456789
     * @bodyParam bank_account_name string required Tên chủ tài khoản. Example: NGUYEN VAN A
     * @bodyParam user_note string nullable Ghi chú của người dùng. Example: Rút hoa hồng tháng này
     *
     * @response 200 {
     *   "status": 200,
     *   "message": "Gửi yêu cầu rút tiền thành công!",
     *   "data": {
     *       "code": "WD20260911A1B2C",
     *       "amount": 2000000,
     *       "amount_formatted": "2.000.000đ",
     *       "new_wallet_balance": 1500000,
     *       "new_wallet_balance_formatted": "1.500.000đ",
     *       "scheduled_payout_date": "2026-09-17",
     *       "status": "pending",
     *       "bank_name": "Vietcombank",
     *       "bank_account_number": "0123456789",
     *       "bank_account_name": "NGUYEN VAN A"
     *   }
     * }
     */
    public function requestWithdraw(WithdrawRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            $transaction = $this->withdrawService->createWithdrawRequest($user, $data);
            $freshUser = $user->fresh();

            $payoutDateText = $transaction->scheduled_payout_date
                ? Carbon::parse($transaction->scheduled_payout_date)->format('d/m/Y')
                : 'Thứ 5 hàng tuần';

            return $this->jsonResponseSuccess([
                'code' => $transaction->code,
                'amount' => (float) $transaction->amount,
                'amount_formatted' => number_format((float) $transaction->amount, 0, ',', '.') . 'đ',
                'tax_amount' => (float) ($transaction->tax_amount ?? 0),
                'tax_amount_formatted' => number_format((float) ($transaction->tax_amount ?? 0), 0, ',', '.') . 'đ',
                'net_amount' => (float) ($transaction->net_amount ?? 0),
                'net_amount_formatted' => number_format((float) ($transaction->net_amount ?? 0), 0, ',', '.') . 'đ',
                'new_wallet_balance' => (float) ($freshUser->wallet_balance ?? 0),
                'new_wallet_balance_formatted' => number_format((float) ($freshUser->wallet_balance ?? 0), 0, ',', '.') . 'đ',
                'scheduled_payout_date' => $transaction->scheduled_payout_date,
                'scheduled_payout_date_formatted' => $payoutDateText,
                'status' => $transaction->status->value,
                'bank_name' => $transaction->bank_name,
                'bank_account_number' => $transaction->bank_account_number,
                'bank_account_name' => $transaction->bank_account_name,
            ], "Gửi yêu cầu rút tiền thành công! Yêu cầu của bạn dự kiến được chi trả vào ngày {$payoutDateText}.");
        } catch (Exception $e) {
            return $this->jsonResponseError($e->getMessage(), 400);
        } catch (Throwable $e) {
            $this->logError('Create withdraw request failed:', $e);
            return $this->jsonResponseError('Đã có lỗi xảy ra khi xử lý yêu cầu rút tiền. Vui lòng thử lại sau.', 500);
        }
    }
}
