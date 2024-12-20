<?php

namespace App\Traits;

use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Enums\Notification\MessageType;
use App\Mail\AdminNotificationMail;
use App\Models\User;
use App\Repositories\Setting\SettingRepositoryInterface;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\Notification;
use Throwable;


trait  NotifiesViaFirebase
{

    /**
     * Sends a Firebase notification to a list of device tokens or a topic.
     *
     * @param array $deviceTokens Array of device tokens to send notification to.
     * @param string|null $topic Topic to send notification to if specified.
     * @param string $title Title of the notification.
     * @param string $body Body text of the notification.
     * @param int|null $notificationId $body Body text of the notification.
     */
    public function sendFirebaseNotification(array  $deviceTokens, ?string $topic,
                                             string $title, string $body, ?int $notificationId = null): void
    {
        $setting = app(SettingRepositoryInterface::class);
        $image = asset($setting->findByField("setting_key", 'site_logo')->plain_value);

        $notificationData = [
            'title' => $title,
            'body' => $body,
            'imageUrl' => $image
        ];

        if ($notificationId !== null) {
            $notificationData['notificationId'] = $notificationId;
        }

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body, $image))
            ->withData($notificationData)
            ->withAndroidConfig(AndroidConfig::fromArray([
                'notification' => [
                    'sound' => 'default'
                ],
            ]))
            ->withApnsConfig(ApnsConfig::fromArray([
                'payload' => [
                    'aps' => [
                        'sound' => 'default'
                    ],
                ]
            ]));

        if ($topic) {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification([
                    'title' => $title,
                    'body' => $body,

                ]);
            $this->sendMessage($message);
        } else {
            foreach ($deviceTokens as $token) {
                $this->sendMessage($message->withChangedTarget('token', $token));
            }
        }
    }

    /**
     * Sends the message using Firebase messaging service.
     *
     * @param mixed $message The Firebase message object.
     */
    private function sendMessage(mixed $message): void
    {
        try {
            $factory = (new Factory)->withServiceAccount(base_path('firebase_credentials.json'));
            $messaging = $factory->createMessaging();
            $messaging->send($message);
            Log::info('Firebase notification sent successfully.');
        } catch (ConnectException $e) {
            Log::error('Network connection issue: Failed to send Firebase notification', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        } catch (RequestException $e) {
            Log::error('HTTP request issue: Failed to send Firebase notification', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        } catch (Throwable $e) {
            Log::error('Failed to send Firebase notification', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }


    /**
     * Registers a device token to a specific Firebase topic.
     *
     * @param string $deviceToken The device token to register.
     * @param string $topic The topic to subscribe to.
     * @return bool True if registration succeeded, false otherwise.
     */
    public function registerDeviceToTopic(string $deviceToken, string $topic): bool
    {
        $messaging = app('firebase.messaging');
        try {
            $messaging->subscribeToTopic($topic, [$deviceToken]);
            Log::info('Device registered to topic successfully.');
            return true;
        } catch (Throwable $e) {
            Log::error('Failed to register device to topic', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Sends notifications to admins with a custom title and body.
     *
     * @param string $title Custom title of the notification.
     * @param string $body Custom body text of the notification.
     * @throws Exception
     */
    public function sendNotificationsToAdmins(string $title, string $body, $type, bool $sendEmail = false): void
    {
        $adminRepository = app(AdminRepositoryInterface::class);
        $notificationRepository = app(NotificationRepositoryInterface::class);
        $setting = app(SettingRepositoryInterface::class);
        $email = $setting->findByField("setting_key", 'email')->plain_value;

        $admins = $adminRepository->getAll();
        $deviceTokens = $admins->pluck('device_token')->filter()->all();
        $firstNotificationId = null;
        if (!empty($deviceTokens)) {
            $this->sendFirebaseNotification($deviceTokens, null, $title, $body);
        }

        foreach ($admins as $admin) {
            $notification = $notificationRepository->create([
                'admin_id' => $admin->id,
                'title' => $title,
                'message' => $body,
                'type' => $type,
            ]);
            $firstNotificationId = $notification->id;
        }
        if ($sendEmail) {
            Mail::to($email)->send(new AdminNotificationMail($title, $body, $firstNotificationId));
        }
    }

    public function sendNotificationsPaymentToAdmins($user, $image, $packageId): void
    {
        $adminRepository = app(AdminRepositoryInterface::class);
        $notificationRepository = app(NotificationRepositoryInterface::class);
        $title = config('notifications.admin_approval_required.title');
        $message = config('notifications.admin_approval_required.message');
        $body = str_replace('{fullname}', $user->fullname, $message);
        $admins = $adminRepository->getAll();
        $deviceTokens = $admins->pluck('device_token')->filter()->all();
        if (!empty($deviceTokens)) {
            $this->sendFirebaseNotification($deviceTokens, null, $title, $body);
        }

        foreach ($admins as $admin) {
            $notificationRepository->create([
                'admin_id' => $admin->id,
                'user_id_attribute' => $user->id,
                'title' => $title,
                'package_id' => $packageId,
                'message' => $body,
                'type' => MessageType::PAYMENT,
                'payment_confirmation_image' => $image
            ]);

        }

    }

    public function sendFirebaseNotificationsToAdmins(string $title, string $body): void
    {
        $adminRepository = app(AdminRepositoryInterface::class);
        $admins = $adminRepository->getAll();
        $deviceTokens = $admins->pluck('device_token')->filter()->all();
        if (!empty($deviceTokens)) {
            $this->sendFirebaseNotification($deviceTokens, null, $title, $body);
        }
    }


    public function sendFirebaseNotificationToUser(User $user, string $title, string $body, ?MessageType $type = null): void
    {
        $notificationRepository = app(NotificationRepositoryInterface::class);

        $deviceToken = $user->device_token;
        $notificationData = [
            'user_id' => $user->id,
            'title' => $title,
            'message' => $body
        ];

        if (!is_null($type)) {
            $notificationData['type'] = $type->value;
        }

        $notification = $notificationRepository->create($notificationData);

        if (!empty($deviceToken)) {
            $this->sendFirebaseNotification([$deviceToken], null, $title, $body, $notification->id,);
        }
    }


}
