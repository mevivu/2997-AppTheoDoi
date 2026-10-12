<?php

namespace App\Api\V1\Services\Journal;

use Illuminate\Http\Request;

interface JournalMilestoneServiceInterface
{
    public function getMilestonesForChild(Request $request): array;
}
