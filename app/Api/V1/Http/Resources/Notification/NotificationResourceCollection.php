<?php

namespace App\Api\V1\Http\Resources\Notification;

use App\Api\V1\Http\Resources\Notification\NotificationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;
class NotificationResourceCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        $userId = auth('api')->id() ?? $this->collection->first()?->user_id;
        $unreadCount = $userId
            ? \App\Models\Notification::where('user_id', $userId)
                ->where(function ($q) {
                    $q->where('status', \App\Enums\Notification\NotificationStatus::NOT_READ)
                      ->orWhere('status', 1)
                      ->orWhereNull('status');
                })
                ->count()
            : 0;

        return [
            'notification' => $this->collection->map(function ($item) {
                $statusVal = $item->status instanceof \App\Enums\Notification\NotificationStatus
                    ? $item->status->value
                    : ($item->status ?? 1);

                $data = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'message' => $item->message,
                    'status' => $statusVal,
                    'created_at' => Carbon::parse($item->created_at)->format('d-m-Y H:i:s'),
                    'user' => $item->user->fullname,
                ];
                return $data;
            }),
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $this->currentPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'limit' => $this->perPage(),
                'total' => $this->total(),
                'count' => $this->count(),
                'total_pages' => $this->lastPage(),
                'unread_count' => $unreadCount,
            ],
        ];
    }
}
