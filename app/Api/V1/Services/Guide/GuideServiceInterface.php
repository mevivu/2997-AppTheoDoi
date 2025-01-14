<?php

namespace App\Api\V1\Services\Guide;

use Illuminate\Pagination\LengthAwarePaginator;

interface GuideServiceInterface
{
    public function getGuides(array $data): LengthAwarePaginator;
}
