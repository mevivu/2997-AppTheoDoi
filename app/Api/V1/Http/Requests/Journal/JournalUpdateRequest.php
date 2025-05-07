<?php

namespace App\Api\V1\Http\Requests\Journal;



use App\Api\V1\Http\Requests\BaseRequest;

class JournalUpdateRequest extends BaseRequest
{


    protected function methodPost(): array
    {
        return [
            'id' => 'required|integer|exists:journals,id',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|array|min:1',
            'image.*' => 'file|image|max:5000',
            'created_at' => 'nullable|date_format:Y-m-d H:i:s',

        ];
    }


}
