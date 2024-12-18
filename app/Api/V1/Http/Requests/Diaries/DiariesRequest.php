<?php

namespace App\Api\V1\Http\Requests\Diaries;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Child\BornStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class DiariesRequest extends BaseRequest
{


    protected function methodPost(): array
    {
        return [

            'title' => ['required', 'string'],
            'content' => ['required','string'],
            'image' => ['required'],
        ];
    }


}
