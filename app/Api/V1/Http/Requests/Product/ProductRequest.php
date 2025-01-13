<?php

namespace App\Api\V1\Http\Requests\Product;

use App\Api\V1\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Enum;

class ProductRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'product_catalog_id' => ['nullable', 'integer', 'exists:product_catalog,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'keyword' => ['nullable', 'string', 'max:255'],
        ];
    }

}
