<?php

namespace App\Admin\Http\Controllers\User;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\User\DepositWalletRequest;
use App\Admin\Http\Requests\User\UserRequest;
use App\Admin\Repositories\Package\PackageRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Services\User\UserServiceInterface;
use App\Admin\DataTables\User\UserDataTable;
use App\Admin\DataTables\Transaction\TransactionDatable;
use App\Traits\ResponseController;
use Exception;
use App\Enums\User\{Gender, UserStatus};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    use ResponseController;

    protected PackageRepositoryInterface $packageRepository;

    public function __construct(
        UserRepositoryInterface    $repository,
        UserServiceInterface       $service,
        PackageRepositoryInterface $packageRepository
    )
    {

        parent::__construct();

        $this->repository = $repository;
        $this->packageRepository = $packageRepository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'edit' => 'admin.users.edit',
            'history' => 'admin.users.order.index',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.user.index',
            'create' => 'admin.user.create',
            'edit' => 'admin.user.edit',
            'delete' => 'admin.user.delete',
            'forceDelete' => 'admin.user.forceDelete',
            'history' => 'admin.users.history',
        ];
    }


    public function index(UserDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'gender' => Gender::asSelectArray(),
                'status' => UserStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Khách hàng')),
            ]

        );
    }


    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'gender' => Gender::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Khách hàng'), route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    /**
     * @throws Exception
     */
    public function edit($id): Factory|View|Application
    {

        $instance = $this->repository->findOrFail($id);
        $packages = $this->packageRepository->getByQueryBuilder(
            [
                ['status', 'IN', [\App\Enums\Package\PackageStatus::Active->value, \App\Enums\Package\PackageStatus::Draft->value]],
            ]
        )->get();
        return view(
            $this->view['edit'],
            [
                'user' => $instance,
                'packages' => $packages,
                'gender' => Gender::asSelectArray(),
                'status' => UserStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('Khách hàng'), route($this->route['index']))->add(__('edit')),
            ],
        );
    }

    public function update(UserRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    /**
     * @throws Exception
     */
    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            $response = $this->repository->findOrFail($id);
            return $response->update(['status' => UserStatus::Inactive->value]);
        });
    }

    /**
     * Xóa vĩnh viễn tài khoản và toàn bộ dữ liệu liên quan
     * @throws Exception
     */
    public function forceDelete($id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $result = $this->service->forceDelete($id);
            if ($result) {
                DB::commit();
                return to_route($this->route['index'])->with('success', __('Xóa tài khoản vĩnh viễn thành công.'));
            }
            DB::rollback();
            return back()->with('error', __('notifyFail'));
        } catch (Exception $e) {
            DB::rollback();
            $this->logError("Error during forceDelete operation", $e);
            return back()->with('error', $e->getMessage() ?: __('notifyFail'));
        }
    }

    protected function getActionMultiple(): array
    {
        return [
            'active' => UserStatus::Active->description(),
            'inactive' => UserStatus::Inactive->description(),
            'lock' => UserStatus::Lock->description(),
            'delete' => __('Xóa vĩnh viễn các tài khoản đã chọn'),
        ];
    }


    public function actionMultipleRecode(Request $request): RedirectResponse
    {
        $boolean = $this->service->actionMultipleRecode($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    public function clearNormalTokens(Request $request): RedirectResponse
    {
        $boolean = $this->service->clearNormalTokens();
        if ($boolean) {
            return back()->with('success', __('Đã đăng xuất toàn bộ tài khoản gói thường thành công.'));
        }
        return back()->with('error', __('Thực hiện thất bại.'));
    }

    public function history(TransactionDatable $dataTable)
    {
        $id = request()->route('id');
        $user = $this->repository->findOrFail($id);
        return $dataTable->render(
            $this->view['history'],
            [
                'breadcrumbs' => $this->crums->add(__('Khách hàng'), route($this->route['index']))->add(__('Lịch sử giao dịch')),
                'orderUser' => $user,
            ]
        );
    }

    public function revokeDevice(Request $request, $userId, $deviceId): JsonResponse|RedirectResponse
    {
        $result = $this->service->revokeDevice((int) $userId, (int) $deviceId);

        if ($request->ajax() || $request->wantsJson()) {
            if ($result) {
                $user = $this->repository->findOrFail($userId);
                $activeCount = $user->activeDevices()->count();
                $maxAllowed = $user->getMaxDevicesAllowed();
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => __('Đã giải phóng thiết bị thành công. Khách hàng có thể đăng nhập trên thiết bị mới.'),
                    'data' => [
                        'device_id' => (int) $deviceId,
                        'active_count' => $activeCount,
                        'max_allowed' => $maxAllowed,
                    ]
                ]);
            }
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => __('Giải phóng thiết bị thất bại hoặc không tìm thấy thiết bị.')
            ], 400);
        }

        if ($result) {
            return back()->with('success', __('Đã giải phóng thiết bị thành công. Khách hàng có thể đăng nhập trên thiết bị mới.'));
        }
        return back()->with('error', __('Giải phóng thiết bị thất bại hoặc không tìm thấy thiết bị.'));
    }

    public function revokeAllDevices(Request $request, $userId): JsonResponse|RedirectResponse
    {
        $result = $this->service->revokeAllDevices((int) $userId);

        if ($request->ajax() || $request->wantsJson()) {
            if ($result) {
                $user = $this->repository->findOrFail($userId);
                $maxAllowed = $user->getMaxDevicesAllowed();
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => __('Đã giải phóng toàn bộ thiết bị của khách hàng thành công.'),
                    'data' => [
                        'active_count' => 0,
                        'max_allowed' => $maxAllowed,
                    ]
                ]);
            }
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => __('Thực hiện thất bại.')
            ], 400);
        }

        if ($result) {
            return back()->with('success', __('Đã giải phóng toàn bộ thiết bị của khách hàng thành công.'));
        }
        return back()->with('error', __('Thực hiện thất bại.'));
    }

    /**
     * Nạp tiền vào ví của thành viên
     */
    public function deposit(DepositWalletRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $adminUser = auth('admin')->user();
            $result = $this->service->depositWallet($request, $adminUser);

            $msg = "Nạp thành công {$result['amount_formatted']} vào ví của thành viên {$result['fullname']} (Mã: {$result['code']})!";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => $msg,
                    'data' => $result,
                ]);
            }

            return back()->with('success', $msg);
        } catch (Exception $e) {
            $this->logError('Nạp tiền vào ví user thất bại:', $e);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Có lỗi xảy ra khi nạp tiền vào ví. Vui lòng thử lại.',
                ], 400);
            }

            return back()->with('error', $e->getMessage() ?: 'Có lỗi xảy ra khi nạp tiền vào ví. Vui lòng thử lại.');
        }
    }
}
