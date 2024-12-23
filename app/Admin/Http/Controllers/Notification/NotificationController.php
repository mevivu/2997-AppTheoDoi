<?php

namespace App\Admin\Http\Controllers\Notification;

use App\Admin\DataTables\Notification\NotificationAdminDataTable;
use App\Admin\DataTables\Notification\NotificationPackageDataTable;
use App\Admin\DataTables\Notification\NotificationUserDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Notification\NotificationRequest;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Services\Notification\NotificationServiceInterface;
use App\Admin\Traits\Roles;
use App\Enums\ApprovalStatus;
use App\Enums\Notification\NotificationOption;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Notification\NotificationType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    use Roles;


    protected UserRepositoryInterface $userRepository;


    public function __construct(
        NotificationRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        NotificationServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->service = $service;
    }

    public function getView(): array
    {

        return [
            'user' => 'admin.notifications.user',
            'index' => 'admin.notifications.index',
            'package' => 'admin.notifications.package',
            'create' => 'admin.notifications.create',
            'edit' => 'admin.notifications.edit'
        ];
    }

    public function getRoute(): array
    {

        return [
            'index' => 'admin.notification.index',
            'user' => 'admin.notification.user',
            'package' => 'admin.notifications.package',
            'create' => 'admin.notification.create',
            'edit' => 'admin.notification.edit',
            'delete' => 'admin.page.delete'
        ];
    }

    public function index(NotificationAdminDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('notifications')),
            'actionMultiple' => $actionMultiple,
        ]);
    }

    public function getNotificationByUser(NotificationUserDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render($this->view['user'], [
            'breadcrumbs' => $this->crums->add(__('notifications')),
            'actionMultiple' => $actionMultiple,
        ]);
    }

    public function getNotificationByPackage(NotificationPackageDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render($this->view['package'], [
            'breadcrumbs' => $this->crums->add(__('notifications')),
            'actionMultiple' => $actionMultiple,
        ]);
    }
    public function create(): View|Application
    {
        return view($this->view['create'], [
            'types' => NotificationType::asSelectArray(),
            'options' => NotificationOption::asSelectArray(),
            'status' => NotificationStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('notifications'), route($this->route['index']))->add(__('add'))
        ]);
    }

    /**
     * @throws Exception
     */
    public function edit($id): View|Application
    {
        $notification = $this->repository->findOrFail($id);
        return view($this->view['edit'], [
            'notification' => $notification,
            'types' => NotificationType::asSelectArray(),
            'options' => NotificationOption::asSelectArray(),
            'status' => NotificationStatus::asSelectArray(),
            'approval_status' => ApprovalStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('notifications'), route($this->route['index']))->add(__('edit'))
        ]);
    }

    public function update(NotificationRequest $request): RedirectResponse
    {
        $this->service->update($request);
        return redirect()->route($this->route['index'])->with('success', __('notifySuccess'));
    }

    public function store(NotificationRequest $request): RedirectResponse
    {
        $this->service->store($request);
        return redirect()->route($this->route['index'])->with('success', __('notifySuccess'));
    }

    public function updateDeviceToken(Request $request)
    {
        return $this->service->updateDeviceToken($request);
    }

    public function updateStatus(NotificationRequest $request)
    {
        return $this->service->updateStatus($request);
    }


    /**
     * Get notification for admin
     *
     * @param NotificationRequest $request
     * @return JsonResponse
     */
    public function getNotificationsForAdmin(NotificationRequest $request): JsonResponse
    {
        $notifications = $this->service->getNotifications($request);

        if ($notifications) {
            return response()->json([
                'notifications' => $notifications
            ]);
        }
        return response()->json([
            'notifications' => [],
            'errors' => ['Specific condition is not met']
        ], 422);
    }

    protected function getActionMultiple(): array
    {
        return [
            'read' => NotificationStatus::READ->description(),
            'not_read' => NotificationStatus::NOT_READ->description(),
            'deleted' => 'Xóa vĩnh viễn',
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

    /**
     * @throws Exception
     */
    public function delete($id): RedirectResponse
    {
        $this->repository->delete($id);
        return redirect()->route($this->route['index'])->with('success', __('notifySuccess'));
    }
}
