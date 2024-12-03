<?php

namespace App\Admin\Http\Requests\VaccinationSchedule;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
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
            'performed_on' => ['required','date'],
            'image' => ['nullable', 'string'],
        ];
    }

    protected function methodPut(): array
    {

        return [
            'id' => ['required', 'exists:App\Models\Clinic,id'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'performed_on' => ['required','date'],
            'image' => ['nullable', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }
}
