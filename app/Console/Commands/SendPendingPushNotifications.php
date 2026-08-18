<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Setting\SettingRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FCMNotification;
use Throwable;

class SendPendingPushNotifications extends Command
{
    protected $signature = 'notification:send-push {--hours=72 : Số giờ tối đa được coi là còn hạn (mặc định: 72 giờ / 3 ngày)}';

    protected $description = 'Gửi push notification cho các thông báo chưa được gửi và còn hạn (is_pushed = false, created_at >= threshold)';

    /**
     * Số notification xử lý tối đa mỗi lần chạy.
     */
    private const BATCH_LIMIT = 1000;

    /**
     * Số giờ tối đa để thông báo được coi là còn hạn gửi push.
     */
    private const DEFAULT_MAX_AGE_HOURS = 72;

    /**
     * Số token gửi tối đa mỗi lần gọi sendMulticast().
     */
    private const MULTICAST_CHUNK = 500;

    private ?\Kreait\Firebase\Contract\Messaging $messaging = null;

    public function handle(): int
    {
        $startTime = microtime(true);
        $maxAgeHours = (int) ($this->option('hours') ?: self::DEFAULT_MAX_AGE_HOURS);
        $validThreshold = now()->subHours($maxAgeHours);

        // 1. Tự động đánh dấu bỏ qua các thông báo đã quá hạn (không gửi push làm phiền user)
        $expiredCount = Notification::where('is_pushed', false)
            ->where('created_at', '<', $validThreshold)
            ->update(['is_pushed' => true]);

        if ($expiredCount > 0) {
            $expiredMsg = "Đã bỏ qua {$expiredCount} thông báo quá hạn (> {$maxAgeHours} giờ).";
            $this->warn($expiredMsg);
            Log::channel('notification-push')->warning("[notification:send-push] {$expiredMsg}");
        }

        // 2. Lấy notifications CÒN HẠN chưa push cho USER (ưu tiên mới nhất)
        $pendingNotifications = Notification::where('is_pushed', false)
            ->whereNotNull('user_id')
            ->where('created_at', '>=', $validThreshold)
            ->orderByDesc('id')
            ->limit(self::BATCH_LIMIT)
            ->get();

        if ($pendingNotifications->isEmpty()) {
            $this->info('Không có thông báo còn hạn nào cần gửi push.');
            return self::SUCCESS;
        }

        // Đánh dấu đã pushed ngay lập tức để tránh race condition/overlapping
        Notification::whereIn('id', $pendingNotifications->pluck('id'))
            ->update(['is_pushed' => true]);

        $this->info("Tìm thấy {$pendingNotifications->count()} thông báo còn hạn cần gửi push.");

        // Lấy danh sách user_id duy nhất
        $userIds = $pendingNotifications->pluck('user_id')->unique()->values();

        // Lấy users có device_token
        $usersWithToken = User::whereIn('id', $userIds)
            ->whereNotNull('device_token')
            ->pluck('device_token', 'id'); // [user_id => device_token]

        if ($usersWithToken->isEmpty()) {
            $this->warn('Không có user nào có device_token. Đã đánh dấu tất cả is_pushed = true.');
            return self::SUCCESS;
        }

        // Nhóm notifications theo title + message (để gửi cùng 1 nội dung cho nhiều user)
        $grouped = $pendingNotifications->groupBy(function ($notification) {
            return md5($notification->title . '|' . $notification->message);
        });

        $totalSent = 0;
        $totalFailed = 0;
        $processedCount = $pendingNotifications->count();

        foreach ($grouped as $group) {
            $firstNotification = $group->first();
            $title = $firstNotification->title;
            $message = $firstNotification->message;

            // Lấy tokens của users trong nhóm này
            $tokens = [];

            foreach ($group as $notification) {
                if (isset($usersWithToken[$notification->user_id])) {
                    $tokens[] = $usersWithToken[$notification->user_id];
                }
            }

            // Gửi FCM batch
            if (!empty($tokens)) {
                $tokens = array_unique($tokens);
                $result = $this->sendMulticastBatch($tokens, $title, $message);
                $totalSent += $result['success'];
                $totalFailed += $result['failed'];
            }
        }

        $elapsed = round(microtime(true) - $startTime, 2);
        $summary = "Push notification hoàn tất: {$totalSent} thành công, {$totalFailed} thất bại, " .
            $processedCount . " notifications đã xử lý trong {$elapsed}s";

        $this->info($summary);
        Log::channel('notification-push')->info("[notification:send-push] {$summary}");

        return self::SUCCESS;
    }

    /**
     * Gửi batch FCM qua sendMulticast() — tối đa 500 token/lần.
     * Tự động phát hiện và xóa token hết hạn/không hợp lệ.
     */
    private function sendMulticastBatch(array $tokens, string $title, string $body): array
    {
        $result = ['success' => 0, 'failed' => 0];

        try {
            $setting = app(SettingRepositoryInterface::class);
            $image = asset($setting->findByField("setting_key", 'site_logo')->plain_value);

            $message = CloudMessage::new()
                ->withNotification(FCMNotification::create($title, $body, $image))
                ->withData([
                    'title' => $title,
                    'body' => $body,
                    'imageUrl' => $image,
                ])
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'notification' => ['sound' => 'default'],
                ]))
                ->withApnsConfig(ApnsConfig::fromArray([
                    'payload' => ['aps' => ['sound' => 'default']],
                ]));

            $messaging = $this->getMessaging();

            // Chia token theo batch 500
            $chunks = array_chunk($tokens, self::MULTICAST_CHUNK);

            foreach ($chunks as $tokenBatch) {
                try {
                    $report = $messaging->sendMulticast($message, $tokenBatch);

                    $successCount = $report->successes()->count();
                    $failureCount = $report->failures()->count();
                    $result['success'] += $successCount;
                    $result['failed'] += $failureCount;

                    Log::channel('notification-push')->info("FCM multicast: {$successCount} success, {$failureCount} failed out of " . count($tokenBatch) . " tokens");

                    // Log chi tiết lý do thất bại
                    if ($failureCount > 0) {
                        $failures = $report->failures()->getItems();
                        $errorSamples = [];
                        foreach (array_slice($failures, 0, 5) as $failure) {
                            $errorSamples[] = $failure->error()->getMessage();
                        }
                        Log::channel('notification-push')->error("Failure reasons (first 5): " . implode(' | ', $errorSamples));
                        Log::channel('notification-push')->info("Unknown tokens: " . count($report->unknownTokens()) . ", Invalid tokens: " . count($report->invalidTokens()));
                    }

                    // Xóa token hết hạn/không hợp lệ
                    $invalidTokens = array_merge(
                        $report->unknownTokens(),
                        $report->invalidTokens()
                    );

                    if (!empty($invalidTokens)) {
                        $count = User::whereIn('device_token', $invalidTokens)
                            ->update(['device_token' => null]);
                        Log::channel('notification-push')->warning("Đã xóa {$count} token hết hạn");
                        $this->warn("Đã xóa {$count} token hết hạn/không hợp lệ");
                    }
                } catch (Throwable $e) {
                    $result['failed'] += count($tokenBatch);
                    Log::channel('notification-push')->error("Multicast batch failed: " . $e->getMessage());
                    $this->error("Batch gửi thất bại: " . $e->getMessage());
                }
            }
        } catch (Throwable $e) {
            $result['failed'] += count($tokens);
            Log::channel('notification-push')->error("sendMulticastBatch failed: " . $e->getMessage());
            $this->error("Lỗi gửi push: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Lấy Firebase Messaging instance (cached trong suốt command).
     */
    private function getMessaging(): \Kreait\Firebase\Contract\Messaging
    {
        if ($this->messaging === null) {
            $factory = (new Factory)
                ->withServiceAccount(base_path('firebase_credentials.json'));
            $this->messaging = $factory->createMessaging();
        }
        return $this->messaging;
    }
}
