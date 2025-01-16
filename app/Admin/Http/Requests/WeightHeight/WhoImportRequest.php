<?php

namespace App\Admin\Http\Requests\WeightHeight;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class WhoImportRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'excelFile' => 'required|file|mimes:xlsx,xls',
            'gender' => ['required', new Enum(Gender::class)],
        ];
    }


    public function messages(): array
    {
        return [
            'excelFile.required' => 'Vui lòng chọn một file để tải lên.',
            'excelFile.file' => 'Đối tượng phải là một file.',
            'excelFile.mimes' => 'File phải có định dạng: xlsx, xls.',
            'gender.required' => 'Vui lòng chọn giới tính.',
            'gender.enum' => 'Giới tính chọn không hợp lệ.',
        ];
    }
}
