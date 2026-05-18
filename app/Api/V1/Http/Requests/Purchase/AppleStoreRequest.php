<?php

namespace App\Api\V1\Http\Requests\Purchase;

use App\Api\V1\Http\Requests\BaseRequest;


class AppleStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'product_id' => 'required|string|exists:packages,code',
            'transaction_id' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'code.exists' => 'Mã gói dịch vụ không tồn tại trong hệ thống.',
            'required' => 'Trường :attribute là bắt buộc.',
            'string' => 'Trường :attribute phải là chuỗi.',
            'integer' => 'Trường :attribute phải là số nguyên.',
            'in' => 'Trường :attribute có giá trị không hợp lệ.',
            'min' => 'Trường :attribute phải có giá trị tối thiểu :min.',
            'boolean' => 'Trường :attribute phải là giá trị boolean.',
        ];
    }
}
