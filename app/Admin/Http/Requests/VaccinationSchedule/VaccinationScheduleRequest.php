<?php

namespace App\Admin\Http\Requests\VaccinationSchedule;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Permission\PermissionType;
use App\Enums\Vaccination\VaccinationStatus;
use Illuminate\Validation\Rules\Enum;


class VaccinationScheduleRequest extends BaseRequest
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
            'performed_on' => ['required', 'date'],
            'type' => ['required', new Enum(PermissionType::class)],
            'image' => ['nullable', 'string'],
            'vaccination_type_id' => ['required', 'exists:App\Models\VaccinationType,id'],
        ];
    }

    protected function methodPut(): array
    {

        return [
            'id' => ['required', 'exists:App\Models\VaccinationSchedule,id'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'performed_on' => ['required', 'date'],
            'image' => ['nullable', 'string'],
            'type' => ['required', new Enum(PermissionType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'vaccination_type_id' => ['required', 'exists:App\Models\VaccinationType,id'],
            'vaccination_status' => ['required', new Enum(VaccinationStatus::class)],
        ];
    }
}
