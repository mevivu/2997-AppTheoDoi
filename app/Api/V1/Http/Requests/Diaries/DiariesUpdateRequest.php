<?php

namespace App\Api\V1\Http\Requests\Diaries;

use App\Api\V1\Http\Requests\BaseRequest;

class DiariesUpdateRequest extends BaseRequest
{

    protected function methodPost(): array
    {
        return [

            'id' => ['required', 'exists:App\Models\Diary,id'],
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'image' => ['required'],
        ];
    }

}
