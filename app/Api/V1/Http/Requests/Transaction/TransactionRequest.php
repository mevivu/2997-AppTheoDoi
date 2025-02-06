<?php

namespace App\Api\V1\Http\Requests\Transaction;

use App\Api\V1\Http\Requests\BaseRequest;

class TransactionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'page' => ['required', 'integer', 'min:1'],
            'limit' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'page.integer' => 'Trang phải là số nguyên',
            'page.min' => 'Trang phải lớn hơn hoặc bằng 1',
            'limit.integer' => 'Số lượng phải là số nguyên',
            'limit.min' => 'Số lượng phải lớn hơn hoặc bằng 1',
        ];
    }
}
