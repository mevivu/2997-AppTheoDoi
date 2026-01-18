<?php

namespace App\Jobs;

use App\Admin\Services\Notification\NotificationFirebaseServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFirebaseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 30;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public array $deviceTokens,
        public string $title,
        public string $message
    ) {}

    /**
     * Execute the job.
     *
     * @param NotificationFirebaseServiceInterface $firebaseService
     * @return void
     */
    public function handle(NotificationFirebaseServiceInterface $firebaseService): void
    {
        $firebaseService->sendFirebaseNotification(
            $this->deviceTokens,
            null,
            $this->title,
            $this->message
        );
    }
}
