<?php

namespace App\Api\V1\Http\Requests\Journal;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ChildBelongsToUser;

class JournalMilestoneListRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'child_id' => ['required', new ChildBelongsToUser()],
        ];
    }
}
