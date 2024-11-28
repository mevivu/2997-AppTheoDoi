<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class UpdateRequest extends BaseRequest
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
            'email' => ['required', 'email', 'unique:App\Models\User,email,' . $this->user()->id],
            'gender' => ['required', new Enum(Gender::class)],
            'address' => ['nullable'],
            'area_id' => ['nullable', 'exists:areas,id'],
//            'category_id' => ['nullable', 'exists:store_categories'],
            'longitude' => ['nullable'],
            'latitude' => ['nullable'],
            'avatar' => ['nullable'],
        ];
    }
}
