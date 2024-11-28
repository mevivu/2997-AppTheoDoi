<?php

namespace App\Api\V1\Http\Requests\Vehicle;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Vehicle\VehicleType;
use Illuminate\Validation\Rules\Enum;

class VehicleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function methodPost(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'name' => ['required', 'string'],

            // Thông tin xe
            'vehicle_company' => ['nullable', 'string'],
            'vehicle_line_id' => ['required', 'exists:vehicle_lines,id'],
            'type' => ['required', new Enum(VehicleType::class)],
            'price' => ['nullable', 'integer'],
            'seat_number' => ['nullable', 'integer'],
            'license_plate' => ['nullable', 'integer'],
            'license_plate_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

            // Giấy đăng ký xe
            'vehicle_registration_front' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'vehicle_registration_back' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

            // Ảnh xe
            'vehicle_front_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'vehicle_back_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'vehicle_side_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'vehicle_interior_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

            // Bảo hiểm
            'insurance_back_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'insurance_front_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
