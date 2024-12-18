<?php

namespace App\Admin\Http\Requests\Journal;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Journal\JournalType;
use Illuminate\Validation\Rules\Enum;


class JournalRequest extends BaseRequest
{
    protected function methodPost()
    {
        return[
            'child_id'=>['required','exists:App\Models\Child,id'],
            'title'=>['required','string'],
            'content'=>['required','string'],
            'image'=>['nullable'],
            'type'=>['nullable',new Enum(JournalType::class)],
        ];
    }
}
