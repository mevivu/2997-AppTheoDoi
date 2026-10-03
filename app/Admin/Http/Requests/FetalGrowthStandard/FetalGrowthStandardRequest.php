<?php

namespace App\Admin\Http\Requests\FetalGrowthStandard;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;

class FetalGrowthStandardRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'week' => ['required', 'integer', 'min:1', 'max:50', 'unique:fetal_growth_standards,week'],
            'length' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'head_circumference' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:fetal_growth_standards,id'],
            'week' => ['required', 'integer', 'min:1', 'max:50', 'unique:fetal_growth_standards,week,' . $this->id],
            'length' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'head_circumference' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }
}
