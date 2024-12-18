<?php

namespace App\Api\V1\Http\Requests\Journal;

use App\Admin\Http\Requests\BaseRequest;


class JournalUpdateRequest extends BaseRequest
{


    protected function methodPost(): array
    {
        return [
            'id' => 'required|integer|exists:journals,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'required|array|min:1',
            'image.*' => 'file|image|max:5000',
        ];
    }


}
