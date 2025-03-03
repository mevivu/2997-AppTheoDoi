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
            'gallery' => ['required', 'array', 'min:1'],
            'gallery.*' => ['required', 'string'],
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
            'gallery' => ['required', 'array', 'min:1'],
            'gallery.*' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'brand_id' => ['required', 'exists:App\Models\Brand,id'],
            'status' => ['required', new Enum(ProductStatus::class)],
            'product_catalog_id' => ['nullable', 'array'],
            'product_catalog_id.*' => ['nullable', 'exists:App\Models\ProductCatalog,id'],
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Trường tên sản phẩm là bắt buộc.',
            'image.required' => 'Trường hình ảnh là bắt buộc.',
            'link.required' => 'Trường liên kết là bắt buộc.',
            'brand_id.required' => 'Trường thương hiệu là bắt buộc.',
            'brand_id.exists' => 'Thương hiệu không tồn tại.',
            'status.required' => 'Trạng thái sản phẩm là bắt buộc.',
            'gallery.required' => 'Bạn cần chọn ít nhất một hình ảnh cho thư viện.',
            'gallery.array' => 'Dữ liệu không hợp lệ, vui lòng thử lại.',
            'gallery.min' => 'Bạn cần chọn ít nhất một hình ảnh.',
            'gallery.*.required' => 'Trường hình ảnh trong thư viện không được để trống.',
            'product_catalog_id.*.exists' => 'Mục lục sản phẩm không tồn tại.'
        ];
    }


}
