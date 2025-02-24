<?php

namespace App\Api\V1\Http\Requests\Child;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Child\BornStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class ChildSyncRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'children' => 'required|array',
            'children.*.id' => 'required',
            'children.*.fullname' => 'required|string|max:255',
            'children.*.gender' => ['required', new Enum(Gender::class)],
            'children.*.is_born' => ['required', new Enum(BornStatus::class)],
            'children.*.birthday' => 'nullable|date',
            'children.*.avatar' => 'nullable|file|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }
}
