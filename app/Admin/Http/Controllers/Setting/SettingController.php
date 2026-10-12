<?php

namespace App\Admin\Http\Controllers\Setting;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\AES\AESHelper;
use App\Enums\Setting\SettingGroup;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Admin\Services\File\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    protected UserRepositoryInterface $userRepository;
    public function __construct(
        SettingRepositoryInterface $repository,
        UserRepositoryInterface $userRepository
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function getView(): array
    {
        return [
            'general' => 'admin.settings.general',
            'system' => 'admin.settings.system',
            'affiliate' => 'admin.settings.affiliate',
            'parent_rank' => 'admin.settings.parent-rank',
        ];
    }

    public function general(): Factory|View|Application
    {
        $settings = $this->repository->getByGroup([SettingGroup::General]);

        return view($this->view['general'], compact('settings'));
    }


    public function system(): Factory|View|Application
    {
        $settings = $this->repository->getByGroup([SettingGroup::System]);
        return view($this->view['system'], [
            'settings' => $settings
        ]);
    }

    public function affiliate(): Factory|View|Application
    {
        $settings = $this->repository->getByGroup([SettingGroup::Affiliate]);
        return view($this->view['affiliate'], [
            'settings' => $settings
        ]);
    }

    public function parentRank(): Factory|View|Application
    {
        $settings = $this->repository->getByGroup([SettingGroup::ParentRank]);
        return view($this->view['parent_rank'], [
            'settings' => $settings
        ]);
    }


    public function update(Request $request): RedirectResponse
    {
        $data = $request->except('_token', '_method');
        $aesSecretKey = $data['aes_secret_key'] ?? null;
        if($aesSecretKey){
            $aesSelectKeyOld = $this->repository->getBy(['setting_key' => 'aes_secret_key'])->first()->plain_value;
            if ($aesSelectKeyOld !== $data['aes_secret_key']) {
                $users = $this->userRepository->getAll();

                foreach ($users as $user) {
                    $usernameDecrypted = AESHelper::decrypt($user->username, $aesSelectKeyOld);
                    $emailDecrypted = AESHelper::decrypt($user->email, $aesSelectKeyOld);
                    $phoneDecrypted = AESHelper::decrypt($user->phone, $aesSelectKeyOld);
                    $addressDecrypted = $user->address ? AESHelper::decrypt($user->address, $aesSelectKeyOld) : null;

                    $user->username = AESHelper::encrypt($usernameDecrypted, $data['aes_secret_key']);
                    $user->email = AESHelper::encrypt($emailDecrypted, $data['aes_secret_key']);
                    $user->phone = AESHelper::encrypt($phoneDecrypted, $data['aes_secret_key']);
                    if ($addressDecrypted) {
                        $user->address = AESHelper::encrypt($addressDecrypted, $data['aes_secret_key']);
                    }

                    $user->save();
                }
            }
        }
        $this->repository->updateMultipleRecord($data);
        return back()->with('success', __('notifySuccess'));
    }

    /**
     * Upload video hướng dẫn trực tiếp lên Cloudflare R2
     *
     * @param Request $request
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function uploadVideo(Request $request, FileService $fileService): JsonResponse
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,mov,avi,webm,mkv|max:204800', // Tối đa 200MB
        ], [
            'video.required' => 'Vui lòng chọn file video cần upload.',
            'video.file' => 'Dữ liệu tải lên không hợp lệ.',
            'video.mimes' => 'Định dạng video phải là mp4, mov, avi, webm hoặc mkv.',
            'video.max' => 'Dung lượng video tối đa là 200MB.',
        ]);

        try {
            $file = $request->file('video');
            $result = $fileService->uploadVideoToR2($file, 'videos/evaluations');

            return response()->json([
                'status' => 200,
                'message' => 'Upload video thành công lên Cloudflare R2!',
                'data' => [
                    'url' => $result['url'],
                    'path' => $result['path'],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('[SettingController::uploadVideo] ' . $e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'Lỗi upload video lên Cloudflare R2: ' . $e->getMessage(),
            ], 500);
        }
    }
}
