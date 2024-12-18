<?php

namespace App\Api\V1\Http\Requests\Diaries;

use App\Api\V1\Http\Requests\BaseRequest;


class DiariesRequest extends BaseRequest
{


    protected function methodPost(): array
    {
        return [

            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'image' => ['required'],
        ];
    }

    protected function methodPut(): array
    {
        return [

            'id' => ['required', 'exists:App\Models\Diary,id'],
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'image' => ['nullable'],
        ];
    }

}
