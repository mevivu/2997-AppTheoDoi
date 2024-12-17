<?php

namespace App\Api\V1\Http\Requests\Child;

use App\Api\V1\Http\Requests\BaseRequest;

class ChildRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [

            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'image' => ['required', 'string'],
        ];
    }
}
