<?php

namespace App\Admin\Http\Requests\ProductCatalog;

use App\Admin\Http\Requests\BaseRequest;
use App\Admin\Rules\ProductCatalogNameUnique;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;


class ProductCatalogRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', new ProductCatalogNameUnique()],
        ];
    }

    protected function methodPut(): array
    {

        return [
            'id' => ['required', 'exists:App\Models\ProductCatalog,id'],
            'name' => ['required', 'string', new ProductCatalogNameUnique($this->id)],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }
}
