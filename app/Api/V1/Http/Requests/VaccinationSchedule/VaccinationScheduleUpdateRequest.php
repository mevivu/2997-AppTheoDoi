<?php

namespace App\Api\V1\Http\Requests\VaccinationSchedule;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Vaccination\VaccinationStatus;
use Illuminate\Validation\Rules\Enum;


class VaccinationScheduleUpdateRequest extends BaseRequest
{


    protected function methodPost(): array
    {
        return [
            'id' => 'required|integer|exists:vaccination_schedules,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'vaccination_status' => ['required', new Enum(VaccinationStatus::class)],
            'performed_on' => ['nullable', 'date_format:d-m-Y'],
            'image' => ['file', 'image', 'max:5000'],
        ];
    }
}
