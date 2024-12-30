<?php

namespace App\Api\V1\Http\Requests\VaccinationSchedule;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Vaccination\VaccinationStatus;
use Illuminate\Validation\Rules\Enum;

class VaccinationScheduleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'limit' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
        ];
    }

    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'vaccination_status' => ['required', new Enum(VaccinationStatus::class)],
            'description' => ['nullable', 'string'],
            'performed_on' => ['nullable', 'date_format:d-m-Y'],
            'image' => ['required', 'array', 'min:1'],
            'image.*' => ['file', 'image', 'max:5000'],
        ];
    }
}
