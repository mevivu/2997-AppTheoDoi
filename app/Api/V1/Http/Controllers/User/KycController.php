<?php

namespace App\Api\V1\Http\Controllers\User;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\File\FileService;
use App\Api\V1\Http\Requests\User\KycUpdateRequest;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @group Xác minh danh tính KYC (CCCD & MST)
 */
class KycController extends Controller
{
    use Response, UseLog;

    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->middleware('auth:api');
        $this->fileService = $fileService;
    }

    /**
     * Định dạng URL ảnh CCCD đảm bảo trả về đường dẫn đầy đủ hợp lệ
     */
    protected function formatIdCardUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'public/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        // Tương thích ngược với các ảnh cũ đã lưu dạng kyc/{userId}/...
        return asset('storage/' . $path);
    }

    /**
     * Lấy trạng thái xác minh KYC hiện tại của người dùng
     *
     * @authenticated
     * @response 200 {
     *   "status": 200,
     *   "message": "Thực hiện thành công.",
     *   "data": {
     *       "kyc_completed": false,
     *       "id_card_front": null,
     *       "id_card_back": null,
     *       "tax_code": null,
     *       "kyc_verified_at": null
     *   }
     * }
     */
    public function getStatus(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return $this->jsonResponseSuccess([
                'kyc_completed' => $user->hasCompletedKyc(),
                'id_card_front' => $this->formatIdCardUrl($user->id_card_front),
                'id_card_back' => $this->formatIdCardUrl($user->id_card_back),
                'tax_code' => $user->tax_code,
                'kyc_verified_at' => $user->kyc_verified_at?->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            $this->logError('Get KYC status failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    /**
     * Cập nhật thông tin KYC: Upload ảnh CCCD mặt trước/sau + Mã số thuế (MST)
     * Sử dụng FileService để quản lý lưu trữ và tự động xóa ảnh cũ tương tự như avatar
     *
     * @authenticated
     * @bodyParam id_card_front file Ảnh CCCD mặt trước (JPG/PNG, tối đa 5MB).
     * @bodyParam id_card_back file Ảnh CCCD mặt sau (JPG/PNG, tối đa 5MB).
     * @bodyParam tax_code string required Mã số thuế cá nhân. Example: 8601234567
     *
     * @response 200 {
     *   "status": 200,
     *   "message": "Cập nhật thông tin xác minh thành công!",
     *   "data": {
     *       "kyc_completed": true,
     *       "id_card_front": "http://domain.com/public/uploads/images/kyc/front.jpg",
     *       "id_card_back": "http://domain.com/public/uploads/images/kyc/back.jpg",
     *       "tax_code": "8601234567"
     *   }
     * }
     */
    public function update(KycUpdateRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            // Sử dụng hàm uploadImages có sẵn của FileService để upload các ảnh và tự động xóa ảnh cũ
            $data = $this->fileService->uploadImages('images/kyc', $data, ['id_card_front', 'id_card_back'], $user);

            // Cập nhật thông tin vào user (lọc bỏ các giá trị null nếu không gửi ảnh mới)
            $user->update(array_filter($data, fn($val) => !is_null($val)));
            $user->refresh();

            return $this->jsonResponseSuccess([
                'kyc_completed' => $user->hasCompletedKyc(),
                'id_card_front' => $this->formatIdCardUrl($user->id_card_front),
                'id_card_back' => $this->formatIdCardUrl($user->id_card_back),
                'tax_code' => $user->tax_code,
            ], 'Cập nhật thông tin xác minh thành công!');
        } catch (Throwable $e) {
            $this->logError('Update KYC failed:', $e);
            return $this->jsonResponseError('Đã có lỗi xảy ra khi cập nhật thông tin xác minh. Vui lòng thử lại.', 500);
        }
    }
}
