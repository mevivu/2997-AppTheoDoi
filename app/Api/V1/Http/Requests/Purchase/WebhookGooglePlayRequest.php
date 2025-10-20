<?php

namespace App\Api\V1\Http\Requests\Purchase;

use App\Api\V1\Http\Requests\BaseRequest;


class WebhookGooglePlayRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [

            'message' => ['required', 'array'],
            'message.data' => ['required', 'string'],
            'message.messageId' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Dữ liệu message là bắt buộc.',
            'message.data.required' => 'Trường message.data (Base64 payload) là bắt buộc.',
        ];
    }

    public function getDecodedData(): ?array
    {
        $data = $this->input('message.data');

        if (!$data) {
            return null;
        }

        $decodedJson = base64_decode($data);
        return json_decode($decodedJson, true);
    }
}
