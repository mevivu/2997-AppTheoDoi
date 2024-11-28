<?php

namespace App\Admin\Http\Requests\User;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\User\Gender;
use App\Enums\User\UserStatus;
use Illuminate\Validation\Rules\Enum;

class UserRequest extends BaseRequest
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
            'phone' => [
                'required',
                'regex:/((09|03|07|08|05)+([0-9]{8})\b)/',
                'unique:App\Models\User,phone'
            ],
            'email' => ['required', 'email', 'unique:App\Models\User,email'],
            'password' => ['required', 'string', 'confirmed'],
            'address' => ['nullable'],
            'name' => ['nullable', 'string'],
            'gender' => ['required', new Enum(Gender::class)],
            'lng' => ['nullable'],
            'lat' => ['nullable'],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'avatar' => ['nullable'],
            'roles' => ['required', 'array'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\User,id'],
            'fullname' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:App\Models\User,email,' . $this->id],
            'phone' => [
                'required',
                'regex:/((09|03|07|08|05)+([0-9]{8})\b)/',
                'unique:App\Models\User,phone,' . $this->id
            ],
            'password' => ['nullable', 'string', 'confirmed'],
            'gender' => ['required', new Enum(Gender::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'avatar' => ['nullable'],
            'status' => ['required', new Enum(UserStatus::class)],
            'lng' => ['nullable'],
            'lat' => ['nullable'],
            'roles' => ['required', 'array'],
            'address' => ['nullable'],
        ];
    }
}
