<?php

namespace App\Admin\Http\Requests\Classes;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;

class ClassesRequest extends BaseRequest
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
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }
    protected function methodPut(): array
    {
        return [
            'id'=>['required', 'integer', 'exists:App\Models\Classes,id'],
            'name' => ['required', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }
}
