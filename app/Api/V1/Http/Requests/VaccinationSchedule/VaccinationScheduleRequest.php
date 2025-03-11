<?php

namespace App\Api\V1\Http\Requests\VaccinationSchedule;

use App\Api\V1\Http\Requests\BaseRequest;
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
            'limit' => 'required|integer|min:1',
            'page' => 'required|integer|min:1',
            'child_id' => ['required', 'exists:children,id'],
            'performed_on' => ['nullable', 'date_format:d-m-Y'],
        ];
    }

    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'vaccination_status' => ['required', new Enum(VaccinationStatus::class)],
            'description' => ['nullable', 'string'],
            'performed_on' => ['nullable', 'date_format:d-m-Y'],
            'image' => ['file', 'image', 'max:5000'],
            'child_id' => ['required', 'exists:children,id'],
        ];
    }


}
