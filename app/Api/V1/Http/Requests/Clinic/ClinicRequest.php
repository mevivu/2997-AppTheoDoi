<?php

namespace App\Api\V1\Http\Requests\Clinic;

use App\Api\V1\Http\Requests\BaseRequest;

class ClinicRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'name'=>['nullable','string'],
            'clinic_type_id'=>['nullable','integer','exists:clinic_types,id'],
            'province_id'=>['nullable','integer','exists:provinces,id'],
            'district_id'=>['nullable','integer','exists:districts,id'],
            'ward_id'=>['nullable','integer','exists:wards,id'],
            'opening_time'=>['nullable','string'],
        ];
    }
}
