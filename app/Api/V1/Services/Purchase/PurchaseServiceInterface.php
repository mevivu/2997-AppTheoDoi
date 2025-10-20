<?php

namespace App\Api\V1\Services\Purchase;

use App\Api\V1\Http\Requests\Purchase\GooglePlayRequest;

interface PurchaseServiceInterface
{

    public function verifyPurchaseGooglePlay(GooglePlayRequest $request);

    public function handleWebhookNotification($data): void;
}
