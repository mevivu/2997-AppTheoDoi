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
        $validate = [];

        if ($this->input('is_born') === BornStatus::Born->value) {
            $validate['birthday'] = ['required', 'date_format:Y-m-d'];
        } else {
            $validate['due_date'] = ['required', 'date_format:Y-m-d'];
        }

        return array_merge($validate, [
            'fullname' => ['required', 'string'],
            'gender' => ['required', new Enum(Gender::class)],
            'is_born' => ['required', new Enum(BornStatus::class)],
            'avatar' => ['nullable'],
        ]);
    }



    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Child,id'],
            'fullname' => ['required', 'string'],
            'gender' => ['required', new Enum(Gender::class)],
            'is_born' => ['required', new Enum(BornStatus::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'due_date' => ['nullable', 'date_format:Y-m-d'],
            'avatar' => ['nullable'],
        ];
    }
}
