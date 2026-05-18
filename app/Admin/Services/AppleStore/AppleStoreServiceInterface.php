<?php

namespace App\Admin\Services\AppleStore;

interface AppleStoreServiceInterface
{
    /**
     * @param string $productId
     * @param string $receiptData
     * @param bool $isSubscription
     * @return array
     */
    public function verifyPurchase(string $productId, string $receiptData, bool $isSubscription = true): array;
}
