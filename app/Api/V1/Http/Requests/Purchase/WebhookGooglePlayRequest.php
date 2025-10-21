<?php

namespace App\Api\V1\Http\Requests\Purchase;

use App\Api\V1\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;

class WebhookGooglePlayRequest extends BaseRequest
{
    /**
     * Prepare data for validation
     */
    protected function prepareForValidation()
    {
        Log::info('📋 Webhook Request Received', [
            'headers' => $this->headers->all(),
            'ip' => $this->ip(),
            'input' => $this->all(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    protected function methodPost(): array
    {
        return [
            'message' => ['required', 'array'],
            'message.data' => ['required', 'string'],
            'message.messageId' => ['nullable', 'string'],
            'message.publishTime' => ['nullable', 'string'],
            'subscription' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'message.required' => 'Dữ liệu message là bắt buộc.',
            'message.array' => 'Message phải là một mảng.',
            'message.data.required' => 'Trường message.data (Base64 payload) là bắt buộc.',
            'message.data.string' => 'Trường message.data phải là chuỗi.',
        ];
    }

    /**
     * Handle failed validation
     * ⚠️ Log lỗi nhưng KHÔNG throw exception
     */
    protected function failedValidation(Validator $validator)
    {
        Log::error('❌ Webhook Validation Failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all(),
        ]);

        // Let parent handle the exception
        parent::failedValidation($validator);
    }

    /**
     * Decode Base64 data from message
     */
    public function getDecodedData(): ?array
    {
        $data = $this->input('message.data');

        if (!$data) {
            Log::warning('⚠️ No message.data in request');
            return null;
        }

        try {
            // Decode Base64
            $decodedJson = base64_decode($data, true);

            if ($decodedJson === false) {
                Log::error('❌ Base64 decode failed');
                return null;
            }

            Log::info('✅ Base64 decoded', [
                'decoded_preview' => substr($decodedJson, 0, 200)
            ]);

            // Parse JSON
            $parsedData = json_decode($decodedJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('❌ JSON decode failed', [
                    'error' => json_last_error_msg()
                ]);
                return null;
            }

            Log::info('✅ JSON parsed successfully', [
                'data' => $parsedData
            ]);

            return $parsedData;

        } catch (\Exception $e) {
            Log::error('❌ Decode exception', [
                'exception' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get notification type
     */
    public function getNotificationType(): ?int
    {
        $decoded = $this->getDecodedData();
        return $decoded['subscriptionNotification']['notificationType'] ?? null;
    }

    /**
     * Check if this is a test notification
     */
    public function isTestNotification(): bool
    {
        $decoded = $this->getDecodedData();
        return isset($decoded['testNotification']);
    }

    /**
     * Check if this is a refund notification
     */
    public function isRefund(): bool
    {
        return $this->getNotificationType() === 12;
    }
}
