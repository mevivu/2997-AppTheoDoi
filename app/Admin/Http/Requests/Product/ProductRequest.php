<?php

namespace App\Admin\Http\Requests\Product;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Product\ProductStatus;
use Illuminate\Validation\Rules\Enum;

class ProductRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'image' => ['required', 'string'],
            'link' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'brand_id' => ['required', 'exists:App\Models\Brand,id'],
            'status' => ['required', new Enum(ProductStatus::class)],
            'product_catalog_id' => ['nullable', 'array'],
            'product_catalog_id.*' => ['nullable', 'exists:App\Models\ProductCatalog,id'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Product,id'],
            'name' => ['required', 'string', 'max:255'],
            'link' => ['required', 'string'],
            'image' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'brand_id' => ['required', 'exists:App\Models\Brand,id'],
            'status' => ['required', new Enum(ProductStatus::class)],
            'product_catalog_id' => ['nullable', 'array'],
            'product_catalog_id.*' => ['nullable', 'exists:App\Models\ProductCatalog,id'],
        ];
    }
}
