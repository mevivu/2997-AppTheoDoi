<?php

namespace App\Api\V1\Http\Requests\User;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Support\AuthServiceApi;
use App\Enums\User\Gender;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;


class UserUpdateRequest extends BaseRequest
{
    use AuthServiceApi;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws Exception
     */

    protected function methodPost(): array
    {
        return [

            'fullname' => ['required'],
            'phone' => [
                'nullable',
                'regex:/((09|03|07|08|05)+([0-9]{8})\b)/',
                Rule::unique('users', 'phone')->ignore($this->user()->id, 'id')
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($this->user()->id, 'id')
            ],
            'avatar' => ['nullable'],
            'gender' => ['nullable', new Enum(Gender::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
        ];
    }

    public function messages()
    {
        return [
            'fullname.required' => 'Tên không được để trống.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng.',
            'birthday.date_format' => 'Ngày sinh không đúng định dạng.',
        ];
    }
}
