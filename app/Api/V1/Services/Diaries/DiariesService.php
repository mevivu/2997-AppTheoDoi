<?php

namespace App\Api\V1\Services\Diaries;

use App\Admin\Repositories\Otp\OtpRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\Roles;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\Diaries\DiariesRepositoryInterface;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Services\User\UserServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Api\V1\Support\OTPEmail;
use App\Api\V1\Support\UseLog;
use App\Enums\User\Gender;
use App\Enums\User\UserStatus;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;


class DiariesService implements DiariesServiceInterface
{

    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected DiariesRepositoryInterface $repository;

    protected FileService $fileService;


    public function __construct(
        DiariesRepositoryInterface $repository,
        FileService                $fileService,
    )
    {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        $avatar = $data['image'];
        $data['user_id'] = $this->getCurrentUserId();
        if ($avatar) {
            $data['avatar'] = $this->fileService
                ->uploadAvatar('images/children', $avatar);
        }
        return $this->repository->create($data);
    }


}
