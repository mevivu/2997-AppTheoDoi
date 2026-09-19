<?php

namespace App\Api\V1\Services\Rating;

use Illuminate\Http\Request;

interface RatingServiceV2Interface
{
    public function storeIQV2(Request $request): object;
}
