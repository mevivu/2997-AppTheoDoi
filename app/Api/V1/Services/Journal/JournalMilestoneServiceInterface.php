<?php

namespace App\Api\V1\Services\Journal;

use App\Models\JournalMilestone;
use Illuminate\Http\Request;

interface JournalMilestoneServiceInterface
{
    public function getMilestonesForChild(Request $request): array;

    public function storeUserMilestone(Request $request): JournalMilestone;

    public function updateUserMilestone(int $id, Request $request): JournalMilestone;

    public function deleteUserMilestone(int $id): bool;
}
