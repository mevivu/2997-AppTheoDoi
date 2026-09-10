<?php

namespace App\Api\V1\Http\Requests\User;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\User;

class ApplyReferralCodeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'referral_code' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $currentUser = auth('api')->user();
                    if (!$currentUser) {
                        $fail('Phiên đăng nhập không hợp lệ hoặc đã hết hạn.');
                        return;
                    }

                    // 1. Kiểm tra tài khoản đã liên kết người giới thiệu trước đó chưa
                    if (!empty($currentUser->referrer_id)) {
                        $fail('Tài khoản của bạn đã được liên kết với người giới thiệu trước đó.');
                        return;
                    }

                    // 2. Kiểm tra mã giới thiệu có tồn tại trong hệ thống không
                    $trimmedCode = trim((string) $value);
                    $referrer = User::where('affiliate_code', $trimmedCode)->first();
                    if (!$referrer) {
                        $fail('Mã giới thiệu không tồn tại trong hệ thống.');
                        return;
                    }

                    // 3. Không cho phép tự nhập mã của chính mình
                    if ($referrer->id === $currentUser->id) {
                        $fail('Bạn không thể tự nhập mã giới thiệu của chính mình.');
                        return;
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'referral_code.required' => 'Vui lòng nhập mã giới thiệu.',
            'referral_code.string' => 'Mã giới thiệu phải là chuỗi ký tự.',
            'referral_code.max' => 'Mã giới thiệu không được vượt quá 50 ký tự.',
        ];
    }
}
