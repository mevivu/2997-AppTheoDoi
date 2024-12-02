<?php

namespace App\Admin\Http\Requests\User;

use App\Admin\Http\Requests\BaseRequest;
use App\Admin\Rules\EmailUnique;
use App\Admin\Rules\PhoneUnique;
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
                new PhoneUnique(),
            ],
            'email' => [
                'required',
                'email',
                new EmailUnique(),
            ],
            'password' => ['required', 'string', 'confirmed'],
            'address' => ['nullable'],
            'name' => ['nullable', 'string'],
            'gender' => ['required', new Enum(Gender::class)],
            'lng' => ['nullable'],
            'lat' => ['nullable'],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'avatar' => ['nullable'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\User,id'],
            'fullname' => ['required', 'string'],
            'email' => [
                'required',
                'email',
                new EmailUnique($this->id),
            ],
            'phone' => [
                'required',
                new PhoneUnique($this->id),
            ],
            'password' => ['nullable', 'string', 'confirmed'],
            'gender' => ['required', new Enum(Gender::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'avatar' => ['nullable'],
            'status' => ['required', new Enum(UserStatus::class)],
            'lng' => ['nullable'],
            'lat' => ['nullable'],
            'address' => ['nullable'],
        ];
    }
}