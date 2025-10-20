<?php

namespace App\Admin\Services\Transaction;

use App\Enums\Transaction\TransactionEnumService;

interface TransactionServiceInterface
{

    public function store($user, $package, $service = TransactionEnumService::NORMAL, $orderId = null, $purchaseToken = null): void;

}
