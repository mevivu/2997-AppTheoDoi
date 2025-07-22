<?php

namespace App\Admin\Http\Requests\Clinic;

use App\Admin\Http\Requests\BaseRequest;

class ClinicImportRequest extends BaseRequest
{
    public function methodPost(): array
    {
        return [
            'excelFile' => 'required|mimes:xlsx,xls,csv|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'excelFile.required' => __('validation.required', ['attribute' => 'file']),
            'excelFile.mimes' => __('validation.mimes', ['attribute' => 'file']),
            'excelFile.max' => __('validation.max.file', ['attribute' => 'file']),
        ];
    }
}
