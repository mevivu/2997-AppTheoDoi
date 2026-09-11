<?php

namespace App\Api\V1\Http\Requests\User;

use App\Api\V1\Http\Requests\BaseRequest;

class KycUpdateRequest extends BaseRequest
{
    /**
     * Validation rules cho upload CCCD mặt trước/sau và Mã số thuế cá nhân (MST)
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'id_card_front' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'id_card_back' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'tax_code' => ['required', 'string', 'max:20', 'regex:/^[0-9\-]+$/'],
        ];
    }

    /**
     * Custom error messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'id_card_front.required' => 'Vui lòng tải lên ảnh CCCD mặt trước.',
            'id_card_front.image' => 'Ảnh CCCD mặt trước phải là file hình ảnh hợp lệ.',
            'id_card_front.mimes' => 'Ảnh CCCD mặt trước chỉ chấp nhận định dạng JPG, JPEG hoặc PNG.',
            'id_card_front.max' => 'Ảnh CCCD mặt trước không được vượt quá 5MB.',
            'id_card_back.required' => 'Vui lòng tải lên ảnh CCCD mặt sau.',
            'id_card_back.image' => 'Ảnh CCCD mặt sau phải là file hình ảnh hợp lệ.',
            'id_card_back.mimes' => 'Ảnh CCCD mặt sau chỉ chấp nhận định dạng JPG, JPEG hoặc PNG.',
            'id_card_back.max' => 'Ảnh CCCD mặt sau không được vượt quá 5MB.',
            'tax_code.required' => 'Vui lòng nhập Mã số thuế cá nhân (MST).',
            'tax_code.max' => 'Mã số thuế không được vượt quá 20 ký tự.',
            'tax_code.regex' => 'Mã số thuế chỉ chấp nhận ký tự số và dấu gạch ngang.',
        ];
    }
}
