<?php

namespace App\Api\V1\Http\Requests\Transaction;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Services\Withdraw\WithdrawService;

class WithdrawRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_name' => ['required', 'string', 'max:100'],
            'user_note' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Kiểm tra logic nghiệp vụ số dư, số tiền rút tối thiểu và bội số rút tiền hoa hồng
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            if (!$user) {
                $validator->errors()->add('user', 'Không tìm thấy thông tin tài khoản người dùng.');
                return;
            }

            // Kiểm tra KYC: yêu cầu CCCD mặt trước/sau + MST đã được Admin phê duyệt
            if (!$user->hasCompletedKyc()) {
                if ($user->isKycPending()) {
                    $validator->errors()->add('kyc', 'Hồ sơ xác minh CCCD của bạn đang chờ Admin phê duyệt trước khi có thể rút tiền.');
                } elseif ($user->isKycRejected()) {
                    $reason = !empty($user->kyc_rejection_reason) ? " Lý do: {$user->kyc_rejection_reason}." : "";
                    $validator->errors()->add('kyc', 'Hồ sơ xác minh CCCD của bạn đã bị từ chối.' . $reason . ' Vui lòng cập nhật lại hồ sơ.');
                } else {
                    $validator->errors()->add('kyc', 'Vui lòng hoàn thành xác minh CCCD và Mã số thuế (MST) trước khi gửi yêu cầu rút tiền.');
                }
                return;
            }

            $amount = (float) $this->input('amount');
            $currentBalance = (float) ($user->wallet_balance ?? 0);

            /** @var WithdrawService $withdrawService */
            $withdrawService = app(WithdrawService::class);
            $config = $withdrawService->getWithdrawSettings();

            // Điều kiện 1: Số dư ví hoa hồng phải đạt tối thiểu min_balance (1.000.000đ)
            if ($currentBalance < $config['min_balance']) {
                $validator->errors()->add(
                    'amount',
                    'Số dư ví hoa hồng của bạn phải đạt tối thiểu ' .
                    $config['min_balance_formatted'] .
                    ' mới có thể yêu cầu rút tiền. (Hiện có: ' .
                    number_format($currentBalance, 0, ',', '.') . 'đ)'
                );
            }

            // Điều kiện 2: Số tiền rút tối thiểu mỗi lần là 1.000.000đ
            if ($amount < $config['min_balance']) {
                $validator->errors()->add(
                    'amount',
                    'Số tiền rút tối thiểu mỗi lần là ' . $config['min_balance_formatted'] . '.'
                );
            }

            // Điều kiện 3: Bắt buộc là bội số của step_multiple
            if ($config['step_multiple'] > 0 && fmod($amount, $config['step_multiple']) != 0) {
                $step = $config['step_multiple'];
                $s1 = number_format($step, 0, ',', '.') . 'đ';
                $s2 = number_format($step * 2, 0, ',', '.') . 'đ';
                $s3 = number_format($step * 3, 0, ',', '.') . 'đ';
                $validator->errors()->add(
                    'amount',
                    'Số tiền rút bắt buộc phải là bội số của ' .
                    $config['step_multiple_formatted'] .
                    " (Ví dụ: {$s1}, {$s2}, {$s3},...).",
                );
            }

            // Điều kiện 4: Không rút vượt quá số dư khả dụng trong ví
            if ($amount > $currentBalance) {
                $validator->errors()->add(
                    'amount',
                    'Số tiền yêu cầu rút (' . number_format($amount, 0, ',', '.') . 'đ) vượt quá số dư khả dụng trong ví.'
                );
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Vui lòng nhập số tiền cần rút.',
            'amount.numeric' => 'Số tiền rút phải là định dạng số hợp lệ.',
            'amount.min' => 'Số tiền rút không hợp lệ.',
            'bank_name.required' => 'Vui lòng chọn hoặc nhập tên ngân hàng nhận tiền.',
            'bank_name.string' => 'Tên ngân hàng không hợp lệ.',
            'bank_name.max' => 'Tên ngân hàng không được vượt quá 100 ký tự.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
            'bank_account_number.string' => 'Số tài khoản không hợp lệ.',
            'bank_account_number.max' => 'Số tài khoản không được vượt quá 50 ký tự.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản ngân hàng.',
            'bank_account_name.string' => 'Tên chủ tài khoản không hợp lệ.',
            'bank_account_name.max' => 'Tên chủ tài khoản không được vượt quá 100 ký tự.',
            'user_note.max' => 'Ghi chú không được vượt quá 255 ký tự.',
        ];
    }
}
