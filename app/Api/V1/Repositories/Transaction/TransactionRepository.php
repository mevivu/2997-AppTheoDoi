<?php

namespace App\Api\V1\Repositories\Transaction;

use \App\Admin\Repositories\Transaction\TransactionRepository as AdminSupport;
use App\Enums\Transaction\TransactionEnumService;


class TransactionRepository extends AdminSupport implements TransactionRepositoryInterface
{


    public function findByOrderIdAndService(string $orderId, TransactionEnumService $service): ?object
    {
        return $this->model
            ->where('google_order_id', $orderId)
            ->where('service', $service)
            ->first();
    }

    public function existsByOrderIdAndService(string $orderId, TransactionEnumService $service): bool
    {
        return $this->model
            ->where('google_order_id', $orderId)
            ->where('service', $service)
            ->exists();
    }
}
