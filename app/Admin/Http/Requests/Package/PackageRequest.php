<?php

namespace App\Admin\Http\Requests\Package;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Package\PackageDiscountType;
use App\Enums\Package\PackageType;
use Illuminate\Validation\Rules\Enum;


class PackageRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],
            'price' => ['required', 'string'],
            'days' => ['required', 'numeric'],
            'type' => ['required', new Enum(PackageType::class)],
            'code' => ['required', 'string'],
            'max_devices' => ['required', 'integer', 'min:1'],
            'discount_type' => ['nullable', new Enum(PackageDiscountType::class)],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'discount_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Package,id'],
            'name' => ['required', 'string'],
            'description' => ['nullable'],
            'price' => ['required', 'string'],
            'days' => ['required', 'numeric'],
            'type' => ['required', new Enum(PackageType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'code' => ['required', 'string'],
            'max_devices' => ['required', 'integer', 'min:1'],
            'discount_type' => ['nullable', new Enum(PackageDiscountType::class)],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'discount_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $discountType = $this->input('discount_type');
            $discountValue = (float) $this->input('discount_value', 0);
            if ($discountType === PackageDiscountType::Percent->value && $discountValue > 100) {
                $validator->errors()->add('discount_value', __('Mức giảm theo phần trăm không được vượt quá 100%.'));
            }
        });
    }
}

