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
    protected $signature = 'notification:send-push';

    protected $description = 'Gửi push notification cho các thông báo chưa được gửi (is_pushed = false)';

    /**
     * Số notification xử lý tối đa mỗi lần chạy.
     */
    private const BATCH_LIMIT = 1000;

    /**
     * Số token gửi tối đa mỗi lần gọi sendMulticast().
     */
    private const MULTICAST_CHUNK = 500;

    private ?\Kreait\Firebase\Contract\Messaging $messaging = null;

    public function handle(): int
    {
        $startTime = microtime(true);

        // Lấy notifications chưa push cho USER (có user_id, không phải admin)
        $pendingNotifications = Notification::where('is_pushed', false)
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->limit(self::BATCH_LIMIT)
            ->get();

        if ($pendingNotifications->isEmpty()) {
            $this->info('Không có thông báo nào cần gửi push.');
            return self::SUCCESS;
        }

        $this->info("Tìm thấy {$pendingNotifications->count()} thông báo cần gửi push.");

        // Lấy danh sách user_id duy nhất
        $userIds = $pendingNotifications->pluck('user_id')->unique()->values();

        // Lấy users có device_token
        $usersWithToken = User::whereIn('id', $userIds)
            ->whereNotNull('device_token')
            ->pluck('device_token', 'id'); // [user_id => device_token]

        if ($usersWithToken->isEmpty()) {
            // Không có user nào có token, đánh dấu tất cả là đã pushed
            Notification::whereIn('id', $pendingNotifications->pluck('id'))
                ->update(['is_pushed' => true]);
            $this->warn('Không có user nào có device_token. Đã đánh dấu tất cả is_pushed = true.');
            return self::SUCCESS;
        }

        // Nhóm notifications theo title + message (để gửi cùng 1 nội dung cho nhiều user)
        $grouped = $pendingNotifications->groupBy(function ($notification) {
            return md5($notification->title . '|' . $notification->message);
        });

        $totalSent = 0;
        $totalFailed = 0;
        $processedIds = [];

        foreach ($grouped as $group) {
            $firstNotification = $group->first();
            $title = $firstNotification->title;
            $message = $firstNotification->message;

            // Lấy tokens của users trong nhóm này
            $tokens = [];
            $notificationIds = [];

            foreach ($group as $notification) {
                $notificationIds[] = $notification->id;

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

            // Đánh dấu đã pushed
            $processedIds = array_merge($processedIds, $notificationIds);
        }

        // Cập nhật tất cả notifications đã xử lý
        if (!empty($processedIds)) {
            Notification::whereIn('id', $processedIds)
                ->update(['is_pushed' => true]);
        }

        $elapsed = round(microtime(true) - $startTime, 2);
        $summary = "Push notification hoàn tất: {$totalSent} thành công, {$totalFailed} thất bại, " .
            count($processedIds) . " notifications đã xử lý trong {$elapsed}s";

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
