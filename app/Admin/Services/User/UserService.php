<?php

namespace App\Admin\Services\User;

use App\Admin\Repositories\Package\PackageRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Repositories\UserSession\UserSessionRepositoryInterface;
use App\Admin\Services\Notification\NotificationFirebaseServiceInterface;
use App\Admin\Traits\Roles;
use App\AES\AESHelper;
use App\Api\V1\Support\UseLog;
use App\Enums\Package\PackageUserStatus;
use App\Enums\Package\PackageType;
use App\Enums\User\UserStatus;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class UserService implements UserServiceInterface
{
    use Setup, Roles, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected UserRepositoryInterface $repository;

    protected PackageRepositoryInterface $packageRepository;

    protected NotificationFirebaseServiceInterface $notificationFirebaseService;

    protected UserSessionRepositoryInterface $userSessionRepository;


    public function __construct(
        UserRepositoryInterface    $repository,
        PackageRepositoryInterface $packageRepository,
        NotificationFirebaseServiceInterface $notificationFirebaseService,
        UserSessionRepositoryInterface $userSessionRepository
    )
    {
        $this->repository = $repository;
        $this->packageRepository = $packageRepository;
        $this->notificationFirebaseService = $notificationFirebaseService;
        $this->userSessionRepository = $userSessionRepository;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();
        $data['code'] = $this->createCodeUser();
        $data['longitude'] = $request['lng'];
        $data['latitude'] = $request['lat'];
        $data['password'] = bcrypt($data['password']);
        $data['email'] = AESHelper::encrypt($data['email']);
        $data['username'] = $data['email'];
        $data['phone'] = AESHelper::encrypt($data['phone']);
        $data['address'] = AESHelper::encrypt($data['address']);
        $user = $this->repository->create($data);

        $data['user_id'] = $user->id;

        //create role
        $this->repository->assignRoles($user, [$this->getRoleCustomer()]);
        return $user;
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {

        $data = $request->validated();
        $data['longitude'] = $request['lng'];
        $data['latitude'] = $request['lat'];
        $packageId = $data['package_id'] ?? null;
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;
        if (isset($data['email'])) {
            $data['email'] = AESHelper::encrypt($data['email']);
        }
        if (isset($data['phone'])) {
            $data['phone'] = AESHelper::encrypt($data['phone']);
        }
        if (isset($data['address'])) {
            $data['address'] = AESHelper::encrypt($data['address']);
        }
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user = $this->repository->findOrFail($data['id']);
        $oldStatus = $user->status;
        $user->update($data);
        if ($user->status !== $oldStatus && ($user->status === UserStatus::Inactive || $user->status === UserStatus::Lock)) {
            try {
                $this->notificationFirebaseService->notifyUserLocked($user);
            } catch (Exception $e) {
                $this->logError('Failed to send lock notification', $e);
            }
        }
        $package = $this->packageRepository->findOrFail($packageId);
        $currentType = $package->type;
        $currentUserPackage = $user->userPackages()->where('status', PackageUserStatus::Active)->first();
        if ($currentUserPackage) {
            $oldType = $currentUserPackage->current_type;
            $currentUserPackage->update([
                'package_id' => $packageId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'current_type' => $currentType
            ]);

            // If package is downgraded/changed to Normal or Trial, and it's different from the old type,
            // invalidate all active sessions to enforce single-device limit
            if (in_array($currentType, [PackageType::Normal, PackageType::Trial]) && $oldType !== $currentType) {
                $this->userSessionRepository->deleteAllSessionTokens($user->id);
            }
        }

        return $user;
    }

    /**
     * @throws Exception
     */
    public function delete($id): object
    {
        return $this->repository->delete($id);

    }

    public function actionMultipleRecode(Request $request): bool
    {
        $this->data = $request->all();
        switch ($this->data['action']) {
            case 'active':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', UserStatus::Active);
                }
                return true;
            case 'inactive':
                foreach ($this->data['id'] as $value) {
                    $user = $this->repository->find($value);
                    if ($user && $user->status !== UserStatus::Inactive) {
                        $this->repository->updateAttribute($value, 'status', UserStatus::Inactive);
                        try {
                            $this->notificationFirebaseService->notifyUserLocked($user);
                        } catch (Exception $e) {
                            $this->logError('Failed to send lock notification', $e);
                        }
                    }
                }
                return true;
            case 'lock':
                foreach ($this->data['id'] as $value) {
                    $user = $this->repository->find($value);
                    if ($user && $user->status !== UserStatus::Lock) {
                        $this->repository->updateAttribute($value, 'status', UserStatus::Lock);
                        try {
                            $this->notificationFirebaseService->notifyUserLocked($user);
                        } catch (Exception $e) {
                            $this->logError('Failed to send lock notification', $e);
                        }
                    }
                }
                return true;

            default:
                return false;
        }
    }

}
