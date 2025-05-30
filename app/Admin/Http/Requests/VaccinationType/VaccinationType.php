<?php

namespace App\Admin\Http\Requests\VaccinationType;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;


class VaccinationType extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'position' => ['required', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)]
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:App\Models\VaccinationType,id'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'position' => ['required', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)]
        ];
    }


}
