<?php

namespace App\Admin\Services\GooglePlay;


interface GooglePlayServiceInterface
{


    public function verifyPurchase($productId, $purchaseToken, $isSubscription = false);


}
