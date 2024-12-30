<?php

namespace App\Api\V1\Http\Requests\VaccinationSchedule;

use App\Admin\Http\Requests\BaseRequest;


class VaccinationScheduleUpdateRequest extends BaseRequest
{


    protected function methodPost(): array
    {
        return [
            'id' => 'required|integer|exists:vaccination_schedules,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'performed_on' => ['required'],
            'image' => 'required|array|min:1',
            'image.*' => 'file|image|max:5000',
        ];
    }
}
