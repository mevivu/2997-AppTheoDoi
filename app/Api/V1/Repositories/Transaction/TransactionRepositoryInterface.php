<?php

namespace App\Api\V1\Repositories\Transaction;

use App\Admin\Repositories\EloquentRepositoryInterface;
use App\Enums\Transaction\TransactionEnumService;

interface TransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function findByOrderIdAndService(string $orderId, TransactionEnumService $service): ?object;

    public function existsByOrderIdAndService(string $orderId, TransactionEnumService $service): bool;


}
