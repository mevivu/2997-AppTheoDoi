<?php

namespace App\Services\Withdraw;

use App\Admin\Repositories\AffiliateHistory\AffiliateHistoryRepositoryInterface;
use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Enums\Notification\MessageType;
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

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        AffiliateHistoryRepositoryInterface $affiliateHistoryRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->affiliateHistoryRepository = $affiliateHistoryRepository;
        $this->userRepository = $userRepository;
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
            : 1000000;

        $stepMultiple = isset($settings['affiliate_withdraw_step_multiple'])
            ? (float) $settings['affiliate_withdraw_step_multiple']
            : 1000000;

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

        // Lấy thông tin tài khoản ngân hàng từ giao dịch rút tiền gần nhất qua repository
        $lastWithdraw = $this->transactionRepository
            ->getQueryBuilderOrderBy('id', 'desc')
            ->where('user_id', $user->id)
            ->where('type', TransactionType::Withdraw)
            ->first();

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
            'saved_bank' => $lastWithdraw ? [
                'bank_name' => $lastWithdraw->bank_name,
                'bank_account_number' => $lastWithdraw->bank_account_number,
                'bank_account_name' => $lastWithdraw->bank_account_name,
            ] : null,
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
        $amount = (float) $data['amount'];
        $config = $this->getWithdrawSettings();

        return DB::transaction(function () use ($user, $amount, $data, $config) {
            // Khóa dòng user để chống race condition / double-spending
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            if (!$lockedUser) {
                throw new Exception('Không tìm thấy tài khoản người dùng.');
            }

            $currentBalance = (float) ($lockedUser->wallet_balance ?? 0);

            // Kiểm tra điều kiện 1: Số dư tối thiểu 1.000.000đ
            if ($currentBalance < $config['min_balance']) {
                throw new Exception(
                    'Số dư ví hoa hồng của bạn phải đạt tối thiểu ' .
                    $config['min_balance_formatted'] .
                    ' mới có thể yêu cầu rút tiền. (Hiện có: ' .
                    number_format($currentBalance, 0, ',', '.') . 'đ)'
                );
            }

            // Kiểm tra điều kiện 2: Số tiền rút tối thiểu
            if ($amount < $config['min_balance']) {
                throw new Exception('Số tiền rút tối thiểu mỗi lần là ' . $config['min_balance_formatted'] . '.');
            }

            // Kiểm tra điều kiện 3: Bắt buộc là bội số của 1.000.000đ
            if ($config['step_multiple'] > 0 && fmod($amount, $config['step_multiple']) != 0) {
                throw new Exception(
                    'Số tiền rút bắt buộc phải là bội số của ' .
                    $config['step_multiple_formatted'] .
                    ' (Ví dụ: 1.000.000đ, 2.000.000đ, 3.000.000đ,...).'
                );
            }

            // Kiểm tra điều kiện 4: Không rút vượt quá số dư hiện có
            if ($amount > $currentBalance) {
                throw new Exception('Số tiền yêu cầu rút (' . number_format($amount, 0, ',', '.') . 'đ) vượt quá số dư khả dụng trong ví.');
            }

            // Sinh mã giao dịch duy nhất
            $code = 'WD' . date('Ymd') . strtoupper(Str::random(5));

            // Trừ tiền khỏi ví khả dụng của đối tác
            $lockedUser->wallet_balance = $currentBalance - $amount;
            $lockedUser->save();

            // Ghi nhận biến động ví vào affiliate_histories qua repository
            $this->affiliateHistoryRepository->create([
                'user_id' => $lockedUser->id,
                'source_user_id' => null,
                'amount' => -$amount,
                'balance_after' => $lockedUser->wallet_balance,
                'type' => 'withdraw_request',
                'description' => "Yêu cầu rút tiền [{$code}] về {$data['bank_name']} (STK: {$data['bank_account_number']}) - Dự kiến chi trả: {$config['next_payout_text']}",
            ]);

            // Tạo giao dịch rút tiền trực tiếp qua hàm riêng của repository
            return $this->transactionRepository->createWithdrawTransaction([
                'code' => $code,
                'user_id' => $lockedUser->id,
                'amount' => $amount,
                'bank_name' => $data['bank_name'],
                'bank_account_number' => $data['bank_account_number'],
                'bank_account_name' => $data['bank_account_name'],
                'scheduled_payout_date' => $config['next_payout_date'],
                'admin_note' => $data['user_note'] ?? null,
            ]);
        });
    }

    /**
     * Admin Duyệt chi trả giao dịch rút tiền (Chuyển sang Confirmed) và gửi thông báo đẩy
     */
    public function approveWithdraw(int $transactionId, $adminUser, ?string $note = null): Transaction
    {
        $transaction = DB::transaction(function () use ($transactionId, $adminUser, $note) {
            $transaction = Transaction::where('id', $transactionId)->lockForUpdate()->first();

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
            $transaction = Transaction::where('id', $transactionId)->lockForUpdate()->first();

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

            // Hoàn lại tiền cho ví đối tác
            $user = User::where('id', $transaction->user_id)->lockForUpdate()->first();
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
                ]
            );
        } catch (Throwable $e) {
            Log::error('Lỗi gửi Firebase notification khi từ chối rút tiền: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
            ]);
        }
    }
}
