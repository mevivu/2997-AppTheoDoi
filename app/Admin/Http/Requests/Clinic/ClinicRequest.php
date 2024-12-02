<?php

namespace App\Admin\Http\Requests\Clinic;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;


class ClinicRequest extends BaseRequest
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
            'address' => ['required', 'string'],
            'hotline' => ['required', 'string'],
            'province' => ['required', 'string'],
            'district' => ['required', 'string'],
            'ward' => ['required', 'string'],
            'clinic_type_id' => ['required', 'exists:App\Models\ClinicType,id'],
            'closing_time' => ['required', 'date_format:H:i'],
            'opening_time' => ['required', 'date_format:H:i'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Clinic,id'],
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'hotline' => ['required', 'string'],
            'province' => ['required', 'string'],
            'district' => ['required', 'string'],
            'ward' => ['required', 'string'],
            'clinic_type_id' => ['required', 'exists:App\Models\ClinicType,id'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'closing_time' => ['required', 'date_format:H:i'],
            'opening_time' => ['required', 'date_format:H:i'],

        ];
    }
}
