<?php

namespace App\Services\Withdraw;

use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Repositories\AffiliateHistory\AffiliateHistoryRepositoryInterface;
use App\Admin\Repositories\Bank\BankRepositoryInterface;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Enums\Notification\MessageType;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\NotifiesViaFirebase;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class WithdrawService
{
    use NotifiesViaFirebase;

    protected TransactionRepositoryInterface $transactionRepository;
    protected AffiliateHistoryRepositoryInterface $affiliateHistoryRepository;
    protected UserRepositoryInterface $userRepository;
    protected BankRepositoryInterface $bankRepository;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        AffiliateHistoryRepositoryInterface $affiliateHistoryRepository,
        UserRepositoryInterface $userRepository,
        BankRepositoryInterface $bankRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->affiliateHistoryRepository = $affiliateHistoryRepository;
        $this->userRepository = $userRepository;
        $this->bankRepository = $bankRepository;
    }

    /**
     * Lấy toàn bộ cấu hình rút tiền từ bảng settings
     */
    public function getWithdrawSettings(): array
    {
        $settings = DB::table('settings')
            ->whereIn('setting_key', [
                'affiliate_withdraw_min_balance',
                'affiliate_withdraw_step_multiple',
                'affiliate_withdraw_payout_day',
                'affiliate_withdraw_payout_note',
            ])
            ->pluck('plain_value', 'setting_key');

        $minBalance = isset($settings['affiliate_withdraw_min_balance'])
            ? (float) $settings['affiliate_withdraw_min_balance']
            : 10000;

        $stepMultiple = isset($settings['affiliate_withdraw_step_multiple'])
            ? (float) $settings['affiliate_withdraw_step_multiple']
            : 10000;

        // Tự động điều chỉnh bội số không vượt quá số dư tối thiểu để tránh xung đột cấu hình
        if ($stepMultiple > $minBalance && $minBalance > 0) {
            $stepMultiple = $minBalance;
        }
        if ($stepMultiple <= 0) {
            $stepMultiple = $minBalance > 0 ? $minBalance : 10000;
        }

        $payoutDayText = $settings['affiliate_withdraw_payout_day'] ?? 'Thứ 5 hàng tuần';
        $payoutNote = $settings['affiliate_withdraw_payout_note'] ?? 'Hệ thống tiếp nhận yêu cầu rút tiền 24/7. Các yêu cầu hợp lệ sẽ được tổng hợp, đối soát và chuyển khoản vào Thứ 5 hàng tuần.';

        // Tính ngày Thứ 5 chi trả dự kiến tiếp theo
        $now = Carbon::now();
        if ($now->isThursday() && $now->hour < 12) {
            $nextPayoutDate = $now->toDateString();
            $nextPayoutText = 'Thứ 5 tuần này (' . $now->format('d/m/Y') . ')';
        } else {
            $nextThursday = $now->copy()->next(Carbon::THURSDAY);
            $nextPayoutDate = $nextThursday->toDateString();
            $nextPayoutText = 'Thứ 5 (' . $nextThursday->format('d/m/Y') . ')';
        }

        return [
            'min_balance' => $minBalance,
            'min_balance_formatted' => number_format($minBalance, 0, ',', '.') . 'đ',
            'step_multiple' => $stepMultiple,
            'step_multiple_formatted' => number_format($stepMultiple, 0, ',', '.') . 'đ',
            'payout_day_text' => $payoutDayText,
            'payout_note' => $payoutNote,
            'next_payout_date' => $nextPayoutDate,
            'next_payout_text' => $nextPayoutText,
        ];
    }

    /**
     * Lấy cấu hình rút tiền kèm thông tin ví và thông tin ngân hàng đã lưu của user
     */
    public function getWithdrawConfigForUser(User $user): array
    {
        $config = $this->getWithdrawSettings();

        // Ưu tiên 1: Lấy thông tin ngân hàng đã cấu hình sẵn trong hồ sơ User
        $savedBank = null;
        if (!empty($user->bank_account_number)) {
            $savedBank = [
                'bank_id' => $user->bank_id,
                'bank_code' => $user->bank_code,
                'bank_name' => $user->bank_name,
                'bank_account_number' => $user->bank_account_number,
                'bank_account_name' => $user->bank_account_name,
                'bank_logo' => $user->bank?->logo,
            ];
        } else {
            // Ưu tiên 2: Fallback lấy thông tin tài khoản ngân hàng từ giao dịch rút tiền gần nhất
            $lastWithdraw = $this->transactionRepository
                ->getQueryBuilderOrderBy('id', 'desc')
                ->where('user_id', $user->id)
                ->where('type', TransactionType::Withdraw)
                ->first();

            if ($lastWithdraw) {
                $savedBank = [
                    'bank_id' => null,
                    'bank_code' => null,
                    'bank_name' => $lastWithdraw->bank_name,
                    'bank_account_number' => $lastWithdraw->bank_account_number,
                    'bank_account_name' => $lastWithdraw->bank_account_name,
                    'bank_logo' => null,
                ];
            }
        }

        $currentBalance = (float) ($user->wallet_balance ?? 0);
        $isEligible = $currentBalance >= $config['min_balance'];

        $maxWithdrawable = 0;
        if ($isEligible && $config['step_multiple'] > 0) {
            $maxWithdrawable = floor($currentBalance / $config['step_multiple']) * $config['step_multiple'];
        }

        return array_merge($config, [
            'wallet_balance' => $currentBalance,
            'wallet_balance_formatted' => number_format($currentBalance, 0, ',', '.') . 'đ',
            'is_eligible' => $isEligible,
            'max_withdrawable' => $maxWithdrawable,
            'max_withdrawable_formatted' => number_format($maxWithdrawable, 0, ',', '.') . 'đ',
            'banks' => $this->bankRepository->getAllBanks(),
            'saved_bank' => $savedBank,
            // Thông tin KYC
            'kyc_completed' => $user->hasCompletedKyc(),
            'id_card_front' => $user->id_card_front,
            'id_card_back' => $user->id_card_back,
            'tax_code' => $user->tax_code,
            // Thuế suất TNCN
            'tax_rate' => 10,
        ]);
    }

    /**
     * Tạo yêu cầu rút tiền trực tiếp vào bảng transactions với Database Transaction & Lock
     *
     * @param User $user
     * @param array $data ['amount', 'bank_name', 'bank_account_number', 'bank_account_name', 'user_note']
     * @return Transaction
     * @throws Exception
     */
    public function createWithdrawRequest(User $user, array $data): Transaction
    {
        // Kiểm tra KYC: yêu cầu CCCD + MST trước khi rút tiền
        if (!$user->hasCompletedKyc()) {
            throw new Exception('Vui lòng hoàn thành xác minh CCCD và Mã số thuế (MST) trước khi gửi yêu cầu rút tiền.');
        }

        $amount = (float) $data['amount'];
        $config = $this->getWithdrawSettings();

        // Tính thuế TNCN 10%
        $taxRate = 10;
        $taxAmount = round($amount * ($taxRate / 100));
        $netAmount = $amount - $taxAmount;

        $transaction = DB::transaction(function () use ($user, $amount, $data, $config, $taxAmount, $netAmount) {
            // Khóa dòng user để chống race condition / double-spending qua repository
            $lockedUser = $this->userRepository->findForUpdate($user->id);

            if (!$lockedUser) {
                throw new Exception('Không tìm thấy tài khoản người dùng.');
            }

            $currentBalance = (float) ($lockedUser->wallet_balance ?? 0);

            // Kiểm tra an toàn kép nếu số dư khả dụng bị thay đổi đồng thời
            if ($amount > $currentBalance) {
                throw new Exception('Số tiền yêu cầu rút (' . number_format($amount, 0, ',', '.') . 'đ) vượt quá số dư khả dụng trong ví.');
            }

            // Sinh mã giao dịch duy nhất
            $code = 'WD' . date('Ymd') . strtoupper(Str::random(5));

            // Trừ tiền khỏi ví khả dụng của đối tác qua repository và lưu thông tin ngân hàng nếu user chưa cấu hình
            $newBalance = $currentBalance - $amount;
            $userUpdateData = [
                'wallet_balance' => $newBalance,
            ];
            if (empty($lockedUser->bank_account_number)) {
                $userUpdateData['bank_name'] = $data['bank_name'] ?? null;
                $userUpdateData['bank_account_number'] = $data['bank_account_number'] ?? null;
                $userUpdateData['bank_account_name'] = $data['bank_account_name'] ?? null;
                if (!empty($data['bank_id'])) {
                    $userUpdateData['bank_id'] = $data['bank_id'];
                }
                if (!empty($data['bank_code'])) {
                    $userUpdateData['bank_code'] = $data['bank_code'];
                }
            }
            $this->userRepository->update($lockedUser->id, $userUpdateData);

            // Ghi nhận biến động ví vào affiliate_histories qua repository
            $this->affiliateHistoryRepository->create([
                'user_id' => $lockedUser->id,
                'source_user_id' => null,
                'amount' => -$amount,
                'balance_after' => $newBalance,
                'type' => 'withdraw_request',
                'description' => "Yêu cầu rút tiền [{$code}] về {$data['bank_name']} (STK: {$data['bank_account_number']}) - Thuế TNCN 10%: " . number_format($taxAmount, 0, ',', '.') . "đ, Thực nhận: " . number_format($netAmount, 0, ',', '.') . "đ - Dự kiến chi trả: {$config['next_payout_text']}",
            ]);

            // Tạo giao dịch rút tiền trực tiếp qua hàm riêng của repository
            return $this->transactionRepository->createWithdrawTransaction([
                'code' => $code,
                'user_id' => $lockedUser->id,
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'net_amount' => $netAmount,
                'bank_name' => $data['bank_name'],
                'bank_account_number' => $data['bank_account_number'],
                'bank_account_name' => $data['bank_account_name'],
                'scheduled_payout_date' => $config['next_payout_date'],
                'admin_note' => $data['user_note'] ?? null,
            ]);
        });

        // Gửi thông báo đẩy Firebase và lưu thông báo hệ thống cho các Admin
        $this->notifyAdminsNewWithdrawRequest($transaction, $user);

        return $transaction;
    }

    /**
     * Admin Duyệt chi trả giao dịch rút tiền (Chuyển sang Confirmed) và gửi thông báo đẩy
     */
    public function approveWithdraw(int $transactionId, $adminUser, ?string $note = null): Transaction
    {
        $transaction = DB::transaction(function () use ($transactionId, $adminUser, $note) {
            $transaction = $this->transactionRepository->findForUpdate($transactionId);

            if (!$transaction || $transaction->status !== TransactionStatus::Pending) {
                throw new Exception('Giao dịch không hợp lệ hoặc đã được xử lý.');
            }

            $updateData = [
                'status' => TransactionStatus::Confirmed,
                'processed_at' => now(),
                'processed_by' => $adminUser->id ?? null,
            ];
            if ($note) {
                $updateData['admin_note'] = $note;
            }

            $this->transactionRepository->update($transaction->id, $updateData);

            return $transaction->fresh();
        });

        // Gửi thông báo đẩy Firebase đến tài khoản đối tác
        $this->notifyUserWithdrawApproved($transaction, $note);

        return $transaction;
    }

    /**
     * Admin Từ chối giao dịch rút tiền (Chuyển sang Refunded, hoàn tiền vào ví) và gửi thông báo đẩy
     */
    public function rejectWithdraw(int $transactionId, $adminUser, string $reason): Transaction
    {
        $transaction = DB::transaction(function () use ($transactionId, $adminUser, $reason) {
            $transaction = $this->transactionRepository->findForUpdate($transactionId);

            if (!$transaction || $transaction->status !== TransactionStatus::Pending) {
                throw new Exception('Giao dịch không hợp lệ hoặc đã được xử lý.');
            }

            // Cập nhật trạng thái giao dịch qua repository
            $this->transactionRepository->update($transaction->id, [
                'status' => TransactionStatus::Refunded,
                'admin_note' => $reason,
                'processed_at' => now(),
                'processed_by' => $adminUser->id ?? null,
            ]);

            // Hoàn lại tiền cho ví đối tác qua repository
            $user = $this->userRepository->findForUpdate($transaction->user_id);
            if ($user) {
                $newBalance = ($user->wallet_balance ?? 0) + $transaction->amount;
                $this->userRepository->update($user->id, ['wallet_balance' => $newBalance]);

                // Ghi nhận hoàn tiền ví vào affiliate_histories qua repository
                $this->affiliateHistoryRepository->create([
                    'user_id' => $user->id,
                    'source_user_id' => null,
                    'amount' => $transaction->amount,
                    'balance_after' => $newBalance,
                    'type' => 'withdraw_refund',
                    'description' => "Hoàn tiền lệnh rút [{$transaction->code}] do bị từ chối: {$reason}",
                ]);
            }

            return $transaction->fresh();
        });

        // Gửi thông báo đẩy Firebase đến tài khoản đối tác
        $this->notifyUserWithdrawRejected($transaction, $reason);

        return $transaction;
    }

    /**
     * Gửi thông báo đẩy khi lệnh rút tiền được duyệt chi trả
     */
    protected function notifyUserWithdrawApproved(Transaction $transaction, ?string $note = null): void
    {
        try {
            $user = $transaction->user ?: User::find($transaction->user_id);
            if (!$user) {
                return;
            }

            $amountFmt = number_format((float) $transaction->amount, 0, ',', '.') . 'đ';
            $title = config('notifications.affiliate_withdraw_approved.title', 'Chi trả hoa hồng thành công');
            $messageTemplate = config(
                'notifications.affiliate_withdraw_approved.message',
                'Lệnh rút tiền {code} ({amount}) của bạn đã được chuyển khoản thành công vào tài khoản {bank_name} ({bank_account_number}).{note}'
            );

            $noteText = !empty($note) ? " Ghi chú/Biên lai: {$note}" : "";
            $body = str_replace(
                ['{code}', '{amount}', '{bank_name}', '{bank_account_number}', '{note}'],
                [$transaction->code, $amountFmt, $transaction->bank_name ?? '', $transaction->bank_account_number ?? '', $noteText],
                $messageTemplate
            );

            $this->sendFirebaseNotificationToUser(
                $user,
                $title,
                $body,
                MessageType::AFFILIATE,
                [
                    'type' => 'withdraw_approved',
                    'transaction_id' => (string) $transaction->id,
                    'code' => $transaction->code,
                    'amount' => (string) $transaction->amount,
                    'balance' => (string) ($user->wallet_balance ?? 0),
                ]
            );
        } catch (Throwable $e) {
            Log::error('Lỗi gửi Firebase notification khi duyệt rút tiền: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
            ]);
        }
    }

    /**
     * Gửi thông báo đẩy khi lệnh rút tiền bị từ chối
     */
    protected function notifyUserWithdrawRejected(Transaction $transaction, string $reason): void
    {
        try {
            $user = $transaction->user ?: User::find($transaction->user_id);
            if (!$user) {
                return;
            }

            $amountFmt = number_format((float) $transaction->amount, 0, ',', '.') . 'đ';
            $title = config('notifications.affiliate_withdraw_rejected.title', 'Yêu cầu rút tiền bị từ chối');
            $messageTemplate = config(
                'notifications.affiliate_withdraw_rejected.message',
                'Lệnh rút tiền {code} ({amount}) đã bị từ chối. Lý do: {reason}. Số tiền {amount} đã được tự động hoàn lại vào ví hoa hồng của bạn.'
            );

            $body = str_replace(
                ['{code}', '{amount}', '{reason}'],
                [$transaction->code, $amountFmt, $reason],
                $messageTemplate
            );

            $this->sendFirebaseNotificationToUser(
                $user,
                $title,
                $body,
                MessageType::AFFILIATE,
                [
                    'type' => 'withdraw_rejected',
                    'transaction_id' => (string) $transaction->id,
                    'code' => $transaction->code,
                    'amount' => (string) $transaction->amount,
                    'balance' => (string) ($user->wallet_balance ?? 0),
                ]
            );
        } catch (Throwable $e) {
            Log::error('Lỗi gửi Firebase notification khi từ chối rút tiền: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
            ]);
        }
    }

    /**
     * Gửi thông báo đẩy và lưu thông báo hệ thống cho các Admin khi có yêu cầu rút tiền mới
     */
    protected function notifyAdminsNewWithdrawRequest(Transaction $transaction, User $user): void
    {
        try {
            $amountFmt = number_format((float) $transaction->amount, 0, ',', '.') . 'đ';
            $title = config('notifications.affiliate_withdraw_requested_admin.title', 'Yêu cầu rút tiền hoa hồng mới');
            $messageTemplate = config(
                'notifications.affiliate_withdraw_requested_admin.message',
                'Đối tác {fullname} vừa tạo yêu cầu rút tiền hoa hồng {amount} về {bank_name} (STK: {bank_account_number}). Mã GD: {code}.'
            );

            $body = str_replace(
                ['{fullname}', '{amount}', '{bank_name}', '{bank_account_number}', '{code}'],
                [$user->fullname ?? 'Đối tác', $amountFmt, $transaction->bank_name ?? '', $transaction->bank_account_number ?? '', $transaction->code],
                $messageTemplate
            );

            $adminRepository = app(AdminRepositoryInterface::class);
            $notificationRepository = app(NotificationRepositoryInterface::class);

            $admins = $adminRepository->getAll();
            $deviceTokens = $admins->pluck('device_token')->filter()->all();

            $fcmData = [
                'type' => 'withdraw_requested',
                'code' => $transaction->code,
                'transaction_id' => (string) $transaction->id,
            ];

            if (!empty($deviceTokens)) {
                $this->sendFirebaseNotification($deviceTokens, null, $title, $body, null, $fcmData);
            }

            foreach ($admins as $admin) {
                $notificationRepository->create([
                    'admin_id' => $admin->id,
                    'user_id_attribute' => $user->id,
                    'title' => $title,
                    'message' => $body,
                    'type' => MessageType::AFFILIATE,
                    'status' => NotificationStatus::NOT_READ,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Lỗi gửi thông báo cho Admin khi tạo yêu cầu rút tiền: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
