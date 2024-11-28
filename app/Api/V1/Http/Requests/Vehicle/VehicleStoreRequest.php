<?php

namespace App\Api\V1\Http\Requests\Vehicle;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Vehicle\VehicleStatus;
use App\Enums\Vehicle\VehicleType;
use Illuminate\Validation\Rules\Enum;

class VehicleStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            // Thông tin cơ bản
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'name' => ['required', 'string'],
            'color' => ['required', 'string'],
            'type' => ['required', new Enum(VehicleType::class)],

            // Giấy đăng ký xe
            'vehicle_registration_front' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'vehicle_registration_back' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

            // Thông tin xe
            'vehicle_company' => ['required', 'string'],
            'seat_number' => ['required', 'integer'],
            'license_plate' => ['required', 'string', 'unique:vehicles,license_plate'],
            'license_plate_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'production_year' => ['nullable','integer'],

            // Bảo hiểm
            'insurance_back_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'insurance_front_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            
            //Dòng xe
            'vehicle_line_id' => ['required', 'exists:vehicle_lines,id'],

            // Ảnh xe
            'vehicle_front_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'vehicle_back_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'vehicle_side_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'vehicle_interior_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

            
        ];
    }
}
