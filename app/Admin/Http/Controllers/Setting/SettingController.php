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
            'evaluation' => 'admin.settings.evaluation',
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

    public function evaluation(): Factory|View|Application
    {
        $settings = $this->repository->getByGroup([SettingGroup::Evaluation]);

        return view($this->view['evaluation'], compact('settings'));
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
     * Upload video hướng dẫn trực tiếp lên Cloudflare R2 (hỗ trợ cả Chunked Multipart 5MB/part & Single Upload)
     *
     * @param Request $request
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function uploadVideo(Request $request, FileService $fileService): JsonResponse
    {
        set_time_limit(600);
        ini_set('max_execution_time', '600');
        ini_set('memory_limit', '512M');

        $action = $request->input('action');

        try {
            if ($action === 'init') {
                $filename = (string) $request->input('filename', 'video.mp4');
                $fileSize = (int) $request->input('file_size', 0);
                $mimeType = (string) $request->input('mime_type', 'video/mp4');

                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($ext, ['mp4', 'mov', 'webm', 'mkv', 'avi', 'm4v'], true)) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Định dạng video phải là mp4, mov, avi, webm hoặc mkv.',
                    ], 422);
                }

                if ($fileSize > 200 * 1024 * 1024) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Dung lượng video tối đa là 200MB.',
                    ], 422);
                }

                $initData = $fileService->initMultipartUploadToR2($filename, 'videos/evaluations', $mimeType);

                return response()->json([
                    'status' => 200,
                    'upload_id' => $initData['upload_id'],
                    'key' => $initData['key'],
                    'url' => $initData['url'],
                ]);
            }

            if ($action === 'part') {
                $request->validate([
                    'upload_id' => 'required|string',
                    'key' => 'required|string',
                    'part_number' => 'required|integer|min:1|max:10000',
                    'chunk' => 'required|file|max:25600',
                ]);

                $partData = $fileService->uploadPartToR2(
                    (string) $request->input('key'),
                    (string) $request->input('upload_id'),
                    (int) $request->input('part_number'),
                    $request->file('chunk')
                );

                return response()->json([
                    'status' => 200,
                    'part_number' => $partData['part_number'],
                    'etag' => $partData['etag'],
                ]);
            }

            if ($action === 'complete') {
                $uploadId = (string) $request->input('upload_id');
                $key = (string) $request->input('key');
                $partsInput = $request->input('parts', []);
                $parts = is_string($partsInput) ? (json_decode($partsInput, true) ?: []) : (array) $partsInput;

                $result = $fileService->completeMultipartUploadToR2($key, $uploadId, $parts);

                return response()->json([
                    'status' => 200,
                    'message' => 'Upload video thành công lên Cloudflare R2!',
                    'url' => $result['url'],
                    'path' => $result['path'],
                    'data' => [
                        'url' => $result['url'],
                        'path' => $result['path'],
                    ],
                ]);
            }

            if ($action === 'abort') {
                $uploadId = (string) $request->input('upload_id');
                $key = (string) $request->input('key');
                if ($uploadId && $key) {
                    $fileService->abortMultipartUploadToR2($key, $uploadId);
                }
                return response()->json(['status' => 200]);
            }

            $request->validate([
                'video' => 'required|file|mimes:mp4,mov,avi,webm,mkv|max:204800', // Tối đa 200MB
            ], [
                'video.required' => 'Vui lòng chọn file video cần upload.',
                'video.file' => 'Dữ liệu tải lên không hợp lệ.',
                'video.mimes' => 'Định dạng video phải là mp4, mov, avi, webm hoặc mkv.',
                'video.max' => 'Dung lượng video tối đa là 200MB.',
            ]);

            $file = $request->file('video');
            $result = $fileService->uploadVideoToR2($file, 'videos/evaluations');

            return response()->json([
                'status' => 200,
                'message' => 'Upload video thành công lên Cloudflare R2!',
                'url' => $result['url'],
                'path' => $result['path'],
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
