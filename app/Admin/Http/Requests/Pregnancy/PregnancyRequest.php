<?php

namespace App\Admin\Http\Requests\Pregnancy;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;


class PregnancyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'exists:App\Models\Child,id'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d'],
            'week' => ['nullable', 'integer'],
            'weight' => ['nullable', 'numeric'],
            'length' => ['nullable', 'integer'],
            'head_circumference' => ['nullable', 'integer'],
            'image' => ['required'],
            'image.*' => ['required', 'string', 'distinct', 'not_in:""'],
            'status' => ['nullable' => new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => 'required|exists:App\Models\Pregnancy,id',
            'child_id' => ['required', 'exists:App\Models\Child,id'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d'],
            'week' => ['nullable', 'integer'],
            'weight' => ['nullable', 'numeric'],
            'length' => ['nullable', 'integer'],
            'head_circumference' => ['nullable', 'integer'],
            'image' => ['required'],
            'image.*' => ['required', 'string', 'distinct', 'not_in:""'],
            'status' => ['nullable' => new Enum(ActiveStatus::class)],
        ];
    }

}
