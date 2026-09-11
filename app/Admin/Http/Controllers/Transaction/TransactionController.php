<?php

namespace App\Admin\Http\Controllers\Transaction;

use App\Admin\DataTables\Transaction\TransactionDatable;
use App\Admin\DataTables\Transaction\TransactionWithdrawDatatable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Admin\Services\Transaction\TransactionServiceInterface;
use App\Services\Withdraw\WithdrawService;
use App\Traits\ResponseController;
use App\Admin\Http\Requests\Transaction\ApproveWithdrawRequest;
use App\Admin\Http\Requests\Transaction\RejectWithdrawRequest;
use App\Traits\RouteAdminSystem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use ResponseController;

    protected WithdrawService $withdrawService;

    public function __construct(
        TransactionRepositoryInterface $repository,
        TransactionServiceInterface    $service,
        WithdrawService                $withdrawService
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

        $this->withdrawService = $withdrawService;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.transaction.index',
            'withdraw' => 'admin.transaction.withdraw',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => RouteAdminSystem::TRANSACTION_INDEX,
            'withdraw' => RouteAdminSystem::TRANSACTION_WITHDRAW,
        ];
    }

    /**
     * Danh sách Giao dịch thanh toán mua gói dịch vụ
     */
    public function index(TransactionDatable $dataTable)
    {
        return $dataTable->render($this->view['index'],
            [
                'breadcrumbs' => $this->crums->add(__('Giao dịch thanh toán mua gói'))
            ]
        );
    }

    /**
     * Danh sách Yêu cầu rút tiền hoa hồng đối tác Affiliate
     */
    public function withdraw(TransactionWithdrawDatatable $dataTable)
    {
        return $dataTable->render($this->view['withdraw'],
            [
                'breadcrumbs' => $this->crums->add(__('Yêu cầu rút tiền'))
            ]
        );
    }

    /**
     * Admin Duyệt chi trả giao dịch rút tiền hoa hồng
     */
    public function approveWithdraw(ApproveWithdrawRequest $request, int $id): JsonResponse
    {
        try {
            $note = $request->input('note');
            $admin = auth('admin')->user();
            $transaction = $this->withdrawService->approveWithdraw($id, $admin, $note);

            return response()->json([
                'status' => 200,
                'message' => __('Duyệt chi trả giao dịch :code thành công!', ['code' => $transaction->code]),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Admin Từ chối giao dịch rút tiền và hoàn tiền vào ví đối tác
     */
    public function rejectWithdraw(RejectWithdrawRequest $request, int $id): JsonResponse
    {
        try {
            $reason = $request->input('reason');
            $admin = auth('admin')->user();
            $transaction = $this->withdrawService->rejectWithdraw($id, $admin, $reason);

            return response()->json([
                'status' => 200,
                'message' => __('Đã từ chối giao dịch :code và hoàn trả :amount vào ví đối tác thành công!', [
                    'code' => $transaction->code,
                    'amount' => number_format((float) $transaction->amount, 0, ',', '.') . 'đ',
                ]),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
