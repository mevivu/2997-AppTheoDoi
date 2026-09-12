<?php

namespace App\Api\V1\Http\Requests\User;

use App\Api\V1\Http\Requests\BaseRequest;

class AcceptAffiliateTermsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'accepted' => [
                'required',
                'boolean',
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        $fail('Bạn cần đồng ý Điều kiện & Điều khoản để tiếp tục.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'accepted.required' => 'Vui lòng xác nhận đồng ý Điều kiện & Điều khoản.',
            'accepted.boolean' => 'Giá trị đồng ý phải là true hoặc false.',
        ];
    }
}
