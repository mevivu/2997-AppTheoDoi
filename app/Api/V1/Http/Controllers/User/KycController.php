<?php

namespace App\Api\V1\Http\Controllers\User;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\User\KycUpdateRequest;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * @group Xác minh danh tính KYC (CCCD & MST)
 */
class KycController extends Controller
{
    use Response, UseLog;

    public function __construct()
    {
        $this->middleware('auth:api');
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
                'id_card_front' => $user->id_card_front ? asset('storage/' . $user->id_card_front) : null,
                'id_card_back' => $user->id_card_back ? asset('storage/' . $user->id_card_back) : null,
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
     *
     * @authenticated
     * @bodyParam id_card_front file required Ảnh CCCD mặt trước (JPG/PNG, tối đa 5MB).
     * @bodyParam id_card_back file required Ảnh CCCD mặt sau (JPG/PNG, tối đa 5MB).
     * @bodyParam tax_code string required Mã số thuế cá nhân. Example: 8601234567
     *
     * @response 200 {
     *   "status": 200,
     *   "message": "Cập nhật thông tin xác minh thành công!",
     *   "data": {
     *       "kyc_completed": true,
     *       "id_card_front": "http://domain.com/storage/kyc/1/front.jpg",
     *       "id_card_back": "http://domain.com/storage/kyc/1/back.jpg",
     *       "tax_code": "8601234567"
     *   }
     * }
     */
    public function update(KycUpdateRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            $storagePath = "kyc/{$user->id}";

            // Upload ảnh CCCD mặt trước
            if ($request->hasFile('id_card_front')) {
                // Xóa ảnh cũ nếu có
                if (!empty($user->id_card_front)) {
                    Storage::disk('public')->delete($user->id_card_front);
                }
                $frontPath = $request->file('id_card_front')->store($storagePath, 'public');
                $user->id_card_front = $frontPath;
            }

            // Upload ảnh CCCD mặt sau
            if ($request->hasFile('id_card_back')) {
                if (!empty($user->id_card_back)) {
                    Storage::disk('public')->delete($user->id_card_back);
                }
                $backPath = $request->file('id_card_back')->store($storagePath, 'public');
                $user->id_card_back = $backPath;
            }

            // Lưu Mã số thuế
            $user->tax_code = $data['tax_code'];
            $user->save();

            return $this->jsonResponseSuccess([
                'kyc_completed' => $user->hasCompletedKyc(),
                'id_card_front' => $user->id_card_front ? asset('storage/' . $user->id_card_front) : null,
                'id_card_back' => $user->id_card_back ? asset('storage/' . $user->id_card_back) : null,
                'tax_code' => $user->tax_code,
            ], 'Cập nhật thông tin xác minh thành công!');
        } catch (Throwable $e) {
            $this->logError('Update KYC failed:', $e);
            return $this->jsonResponseError('Đã có lỗi xảy ra khi cập nhật thông tin xác minh. Vui lòng thử lại.', 500);
        }
    }
}
