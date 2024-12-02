<?php

namespace App\Admin\Http\Requests\Children;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\User\Gender;
use App\Enums\Child\ChildStatus;
use Illuminate\Validation\Rules\Enum;

class ChildrenRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'fullname' => ['required', 'string'],
            'gender' => ['required', new Enum(Gender::class)],
            'status' => ['required', new Enum(ChildStatus::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'user_id' => ['required', 'exists:App\Models\User,id'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'user_id' => ['required', 'exists:App\Models\User,id'],
            'id' => ['required', 'exists:App\Models\Child,id'],
            'fullname' => ['required', 'string'],
            'gender' => ['required', new Enum(Gender::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['required', new Enum(ChildStatus::class)],
        ];
    }
}
