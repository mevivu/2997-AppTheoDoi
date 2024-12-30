<?php

namespace App\Api\V1\Http\Requests\VaccinationSchedule;

use App\Admin\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Permission\PermissionType;

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
            'vaccination_status' => ['required'],
            'vaccination_type_id' => ['required', 'integer', 'exists:vaccination_types,id'],
            'description' => ['nullable', 'string'],
            'performed_on' => ['required'],
            'image' => ['required', 'array', 'min:1'],
            'image.*' => ['file', 'image', 'max:5000'],
        ];
    }
}
