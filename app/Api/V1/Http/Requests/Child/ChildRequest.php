<?php

namespace App\Api\V1\Http\Requests\Child;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Child\BornStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class ChildRequest extends BaseRequest
{

    protected function methodGet(): array
    {
        return [

            'page' => ['required'],
            'limit' => ['required'],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        $isBorn = $this->input('is_born');
        $birthdayRules = ['required', 'date_format:Y-m-d'];

        if ($isBorn === BornStatus::Born->value) {
            $birthdayRules[] = 'before_or_equal:today';
        } elseif ($isBorn === BornStatus::Unborn->value) {
            $birthdayRules[] = 'after_or_equal:today';
        }

        return [
            'fullname' => ['required', 'string'],
            'gender' => ['required', new Enum(Gender::class)],
            'is_born' => ['required', new Enum(BornStatus::class)],
            'avatar' => ['nullable'],
            'birthday' => $birthdayRules,
        ];
    }

    protected function methodPut(): array
    {
        $isBorn = $this->input('is_born');
        $birthdayRules = ['required', 'date_format:Y-m-d'];

        if ($isBorn === BornStatus::Born->value) {
            $birthdayRules[] = 'before_or_equal:today';
        } elseif ($isBorn === BornStatus::Unborn->value) {
            $birthdayRules[] = 'after_or_equal:today';
        }

        return [
            'id' => ['required', 'exists:App\Models\Child,id'],
            'fullname' => ['required', 'string'],
            'gender' => ['required', new Enum(Gender::class)],
            'is_born' => ['required', new Enum(BornStatus::class)],
            'birthday' => $birthdayRules,
            'avatar' => ['nullable'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'birthday.before_or_equal' => __('Ngày sinh không được lớn hơn ngày hiện tại.'),
            'birthday.after_or_equal' => __('Ngày dự sinh không được nhỏ hơn ngày hiện tại.'),
        ];
    }
}
